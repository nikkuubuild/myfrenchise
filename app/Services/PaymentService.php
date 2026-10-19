<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Refund;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected InvoiceService $invoiceService;
    protected NotificationService $notificationService;

    public function __construct(InvoiceService $invoiceService, NotificationService $notificationService)
    {
        $this->invoiceService = $invoiceService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new commercial order for a customer.
     */
    public function createOrder(
        User $user,
        ServicePackage $package,
        array $billingDetails,
        ?Enquiry $enquiry = null
    ): Order {
        $basePaise = $package->base_price_paise;
        $taxPaise = $package->calculateTaxAmount($basePaise);
        $totalPaise = $basePaise + $taxPaise;

        return Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $user->id,
            'service_package_id' => $package->id,
            'enquiry_id' => $enquiry?->id,
            'currency' => 'INR',
            'subtotal_paise' => $basePaise,
            'tax_amount_paise' => $taxPaise,
            'total_amount_paise' => $totalPaise,
            'status' => Order::STATUS_PENDING,
            'billing_name' => $billingDetails['billing_name'],
            'billing_email' => $billingDetails['billing_email'],
            'billing_phone' => $billingDetails['billing_phone'],
            'billing_state' => strtoupper($billingDetails['billing_state']),
            'billing_city' => $billingDetails['billing_city'] ?? null,
            'billing_gstin' => $billingDetails['billing_gstin'] ?? null,
        ]);
    }

    /**
     * Initiate a gateway transaction for an order.
     */
    public function initiateTransaction(
        Order $order,
        string $gateway = PaymentTransaction::GATEWAY_RAZORPAY
    ): PaymentTransaction {
        $mockGatewayOrderId = 'order_'.Str::lower(Str::random(14));

        return PaymentTransaction::create([
            'transaction_reference' => PaymentTransaction::generateReference(),
            'order_id' => $order->id,
            'gateway' => $gateway,
            'gateway_order_id' => $mockGatewayOrderId,
            'amount_paise' => $order->total_amount_paise,
            'currency' => $order->currency,
            'status' => PaymentTransaction::STATUS_INITIATED,
        ]);
    }

    /**
     * Verify payment signature server-side and transition order to paid.
     * Rule: Never trust browser redirect as proof of payment.
     */
    public function verifyAndCapture(
        PaymentTransaction $transaction,
        string $gatewayPaymentId,
        string $signature,
        array $metadata = []
    ): bool {
        $secret = config('services.razorpay.secret', 'test_razorpay_secret_key_abcdef');
        $expectedSignature = hash_hmac('sha256', $transaction->gateway_order_id.'|'.$gatewayPaymentId, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            $transaction->update([
                'status' => PaymentTransaction::STATUS_FAILED,
                'gateway_payment_id' => $gatewayPaymentId,
                'gateway_signature' => $signature,
                'failure_reason' => 'Cryptographic signature mismatch during verification',
            ]);
            Log::warning("Payment signature mismatch for transaction: {$transaction->transaction_reference}");

            return false;
        }

        return DB::transaction(function () use ($transaction, $gatewayPaymentId, $signature, $metadata) {
            $transaction->update([
                'status' => PaymentTransaction::STATUS_CAPTURED,
                'gateway_payment_id' => $gatewayPaymentId,
                'gateway_signature' => $signature,
                'payment_method' => $metadata['payment_method'] ?? 'upi',
                'metadata' => [
                    'verified_at' => now()->toIso8601String(),
                    'gateway_bank' => $metadata['bank'] ?? 'HDFC',
                ],
            ]);

            $order = $transaction->order;
            if (! $order->isPaid()) {
                $order->update([
                    'status' => Order::STATUS_PAID,
                    'paid_at' => now(),
                ]);

                // Generate statutory GST Tax Invoice
                $this->invoiceService->generateInvoice($order);

                // Dispatch Transactional Multi-Channel Notice
                $this->notificationService->notifyPaymentReceived($order);
            }

            return true;
        });
    }

    /**
     * Idempotent server-side webhook processor.
     */
    public function handleWebhook(string $gateway, string $payloadRaw, string $signature): array
    {
        $webhookSecret = config('services.razorpay.webhook_secret', 'test_webhook_secret_key_123456');
        $expectedSignature = hash_hmac('sha256', $payloadRaw, $webhookSecret);

        $isVerified = hash_equals($expectedSignature, $signature);
        if (! $isVerified) {
            return [
                'status' => 'invalid_signature',
                'message' => 'Invalid webhook cryptographic signature',
                'code' => 400,
            ];
        }

        $payload = json_decode($payloadRaw, true) ?? [];
        $eventId = $payload['event_id'] ?? $payload['id'] ?? md5($payloadRaw);
        $eventType = $payload['event'] ?? 'payment.captured';

        // Idempotency check: duplicate event protection
        if (WebhookEvent::isDuplicate($gateway, $eventId)) {
            return [
                'status' => 'duplicate_ignored',
                'message' => 'Event already processed idempotently',
                'code' => 200,
            ];
        }

        $webhookRecord = WebhookEvent::create([
            'gateway' => $gateway,
            'event_id' => $eventId,
            'event_type' => $eventType,
            'payload' => $payload,
            'signature_verified' => true,
            'is_processed' => false,
        ]);

        try {
            if ($eventType === 'payment.captured') {
                $gatewayPaymentId = $payload['payload']['payment']['entity']['id'] ?? null;
                $gatewayOrderId = $payload['payload']['payment']['entity']['order_id'] ?? null;

                if ($gatewayOrderId) {
                    $transaction = PaymentTransaction::where('gateway_order_id', $gatewayOrderId)->first();
                    if ($transaction && ! $transaction->isCaptured()) {
                        $transaction->update([
                            'status' => PaymentTransaction::STATUS_CAPTURED,
                            'gateway_payment_id' => $gatewayPaymentId,
                        ]);

                        $order = $transaction->order;
                        if (! $order->isPaid()) {
                            $order->update([
                                'status' => Order::STATUS_PAID,
                                'paid_at' => now(),
                            ]);
                            $this->invoiceService->generateInvoice($order);
                            $this->notificationService->notifyPaymentReceived($order);
                        }
                    }
                }
            }

            $webhookRecord->update([
                'is_processed' => true,
                'processed_at' => now(),
            ]);

            return [
                'status' => 'processed',
                'message' => 'Webhook handled and state reconciled successfully',
                'code' => 200,
            ];
        } catch (\Throwable $e) {
            $webhookRecord->update([
                'error_message' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    /**
     * Process staff-authorized refund with audit trail.
     */
    public function processRefund(Order $order, int $amountPaise, string $reason, User $staffUser): Refund
    {
        if (! $order->isPaid()) {
            throw new \InvalidArgumentException('Cannot refund an unpaid order.');
        }

        if ($amountPaise > $order->total_amount_paise) {
            throw new \InvalidArgumentException('Refund amount cannot exceed total order value.');
        }

        return DB::transaction(function () use ($order, $amountPaise, $reason, $staffUser) {
            $refund = Refund::create([
                'refund_reference' => Refund::generateReference(),
                'order_id' => $order->id,
                'payment_transaction_id' => $order->latestTransaction?->id,
                'amount_paise' => $amountPaise,
                'reason' => $reason,
                'status' => Refund::STATUS_PROCESSED,
                'gateway_refund_id' => 'rfnd_'.Str::lower(Str::random(14)),
                'approved_by_user_id' => $staffUser->id,
                'processed_at' => now(),
            ]);

            // If full refund, update order status
            $totalRefunded = $order->refunds()->where('status', Refund::STATUS_PROCESSED)->sum('amount_paise');
            if ($totalRefunded >= $order->total_amount_paise) {
                $order->update(['status' => Order::STATUS_REFUNDED]);
            }

            return $refund;
        });
    }
}
