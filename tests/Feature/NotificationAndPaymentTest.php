<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Refund;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Database\Seeders\ServicePackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAndPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected ServicePackage $package;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ServicePackageSeeder::class);
        $this->package = ServicePackage::where('slug', 'commercial-due-diligence')->firstOrFail();
    }

    /**
     * Test checkout page renders transparent fee breakdown and disclosures.
     */
    public function test_checkout_page_renders_transparent_fee_breakdown(): void
    {
        $response = $this->get("/checkout/{$this->package->slug}");
        $response->assertStatus(200);
        $response->assertSee('Managed Legal Due Diligence');
        $response->assertSee('₹4,999.00'); // Base price
        $response->assertSee('GST (18%'); // 18% tax
        $response->assertSee('Service Inclusions');
        $response->assertSee('Critical Limitations &amp; Exclusions', false);
        $response->assertSee('Cancellation &amp; Refund Policy', false);
    }

    /**
     * Test checkout requires authentication and valid statutory declaration.
     */
    public function test_checkout_submission_requires_auth_and_statutory_declaration(): void
    {
        // Unauthenticated guest is redirected to login
        $this->post("/checkout/{$this->package->slug}", [
            'billing_name' => 'Test User',
        ])->assertRedirect('/login');

        $customer = User::factory()->customer()->create();

        // Missing statutory declaration
        $failResponse = $this->actingAs($customer)->post("/checkout/{$this->package->slug}", [
            'billing_name' => 'Rahul Sharma',
            'billing_email' => 'rahul@example.com',
            'billing_phone' => '9876500001',
            'billing_state' => 'MH',
            // Missing statutory_declaration
        ]);
        $failResponse->assertSessionHasErrors(['statutory_declaration']);
        $this->assertDatabaseMissing('orders', ['user_id' => $customer->id]);
    }

    /**
     * Test valid checkout creates pending order and initiates transaction.
     */
    public function test_valid_checkout_creates_order_and_initiates_transaction(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)->post("/checkout/{$this->package->slug}", [
            'billing_name' => 'Rahul Sharma',
            'billing_email' => 'rahul@example.com',
            'billing_phone' => '+91 98765 00001',
            'billing_state' => 'MH',
            'billing_city' => 'Pune',
            'billing_gstin' => '27AAAAA0000A1Z5',
            'statutory_declaration' => '1',
        ]);

        $order = Order::where('user_id', $customer->id)->firstOrFail();
        $response->assertRedirect(route('checkout.pay', $order));

        $this->assertEquals(Order::STATUS_PENDING, $order->status);
        $this->assertEquals(499900, $order->subtotal_paise);
        $this->assertEquals(89982, $order->tax_amount_paise); // 18% of 499900
        $this->assertEquals(589882, $order->total_amount_paise);
        $this->assertStringStartsWith('FD-ORD-', $order->order_number);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $order->id,
            'status' => PaymentTransaction::STATUS_INITIATED,
            'amount_paise' => 589882,
        ]);
    }

    /**
     * Test server rejects payment with forged or invalid signature (never trust browser redirect).
     */
    public function test_payment_rejected_on_cryptographic_signature_mismatch(): void
    {
        $customer = User::factory()->customer()->create();
        $order = app(PaymentService::class)->createOrder($customer, $this->package, [
            'billing_name' => 'Rahul Sharma',
            'billing_email' => 'rahul@example.com',
            'billing_phone' => '9876500001',
            'billing_state' => 'MH',
        ]);
        $transaction = app(PaymentService::class)->initiateTransaction($order);

        // Attempt callback with forged signature
        $response = $this->actingAs($customer)->post("/checkout/{$order->id}/callback", [
            'razorpay_payment_id' => 'pay_tampered_123',
            'razorpay_signature' => 'invalid_forged_signature_hash',
        ]);

        $response->assertRedirect(route('checkout.pay', $order));
        $response->assertSessionHas('error');

        $order->refresh();
        $this->assertEquals(Order::STATUS_PENDING, $order->status);

        $transaction->refresh();
        $this->assertEquals(PaymentTransaction::STATUS_FAILED, $transaction->status);
        $this->assertStringContainsString('signature mismatch', $transaction->failure_reason);
    }

    /**
     * Test valid payment capture marks order as paid, generates GST invoice, and sends notifications.
     */
    public function test_valid_payment_capture_generates_invoice_and_sends_notifications(): void
    {
        $customer = User::factory()->customer()->create();
        $order = app(PaymentService::class)->createOrder($customer, $this->package, [
            'billing_name' => 'Rahul Sharma',
            'billing_email' => 'rahul@example.com',
            'billing_phone' => '9876500001',
            'billing_state' => 'MH', // Maharashtra (inter-state vs Haryana seller)
        ]);
        $transaction = app(PaymentService::class)->initiateTransaction($order);

        $secret = config('services.razorpay.secret');
        $paymentId = 'pay_verified_999';
        $validSignature = hash_hmac('sha256', $transaction->gateway_order_id.'|'.$paymentId, $secret);

        $response = $this->actingAs($customer)->post("/checkout/{$order->id}/callback", [
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $validSignature,
            'payment_method' => 'upi',
        ]);

        $response->assertRedirect(route('customer.orders.show', $order));

        $order->refresh();
        $this->assertEquals(Order::STATUS_PAID, $order->status);
        $this->assertNotNull($order->paid_at);

        $transaction->refresh();
        $this->assertEquals(PaymentTransaction::STATUS_CAPTURED, $transaction->status);
        $this->assertEquals($paymentId, $transaction->gateway_payment_id);

        // Verify Statutory GST Tax Invoice generation
        $this->assertNotNull($order->invoice);
        $invoice = $order->invoice;
        $this->assertStringStartsWith('FD-INV-', $invoice->invoice_number);
        $this->assertTrue($invoice->is_interstate); // MH != HR
        $this->assertEquals(0, $invoice->cgst_paise);
        $this->assertEquals(0, $invoice->sgst_paise);
        $this->assertEquals($order->tax_amount_paise, $invoice->igst_paise); // Inter-state IGST

        // Verify Transactional Notifications Logged
        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $customer->id,
            'event_type' => 'payment_successful',
            'channel' => NotificationLog::CHANNEL_MAIL,
            'status' => NotificationLog::STATUS_DELIVERED,
        ]);
        $this->assertDatabaseHas('notification_logs', [
            'user_id' => $customer->id,
            'event_type' => 'payment_successful',
            'channel' => NotificationLog::CHANNEL_SMS_DLT,
            'dlt_template_id' => 'DLT-FD-PAY-01',
        ]);
    }

    /**
     * Test intra-state GST tax split (Haryana to Haryana -> CGST 9% + SGST 9%).
     */
    public function test_intra_state_gst_invoice_tax_split(): void
    {
        $customer = User::factory()->customer()->create();
        $order = app(PaymentService::class)->createOrder($customer, $this->package, [
            'billing_name' => 'Amit Verma',
            'billing_email' => 'amit@example.com',
            'billing_phone' => '9876500002',
            'billing_state' => 'HR', // Haryana customer (Intra-state)
        ]);
        $transaction = app(PaymentService::class)->initiateTransaction($order);

        $secret = config('services.razorpay.secret');
        $paymentId = 'pay_hr_111';
        $validSignature = hash_hmac('sha256', $transaction->gateway_order_id.'|'.$paymentId, $secret);

        $this->actingAs($customer)->post("/checkout/{$order->id}/callback", [
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $validSignature,
        ]);

        $order->refresh();
        $invoice = $order->invoice;
        $this->assertFalse($invoice->is_interstate);
        $this->assertEquals(0, $invoice->igst_paise);
        $this->assertGreaterThan(0, $invoice->cgst_paise);
        $this->assertGreaterThan(0, $invoice->sgst_paise);
        $this->assertEquals($order->tax_amount_paise, $invoice->cgst_paise + $invoice->sgst_paise);
    }

    /**
     * Test invoice access authorization (IDOR protection).
     */
    public function test_customer_cannot_view_another_customers_invoice(): void
    {
        $customerA = User::factory()->customer()->create();
        $customerB = User::factory()->customer()->create();

        $orderA = app(PaymentService::class)->createOrder($customerA, $this->package, [
            'billing_name' => 'Customer A',
            'billing_email' => 'a@example.com',
            'billing_phone' => '9876500010',
            'billing_state' => 'DL',
        ]);
        $orderA->update(['status' => Order::STATUS_PAID]);
        $invoiceA = app(\App\Services\InvoiceService::class)->generateInvoice($orderA);

        // Customer A can view own invoice
        $this->actingAs($customerA)
            ->get("/customer/invoices/{$invoiceA->id}")
            ->assertStatus(200)
            ->assertSee($invoiceA->invoice_number);

        // Customer B receives 403 Forbidden
        $this->actingAs($customerB)
            ->get("/customer/invoices/{$invoiceA->id}")
            ->assertForbidden();
    }

    /**
     * Test webhook processing with valid signature and idempotency duplicate protection.
     */
    public function test_webhook_processing_and_idempotency_protection(): void
    {
        $customer = User::factory()->customer()->create();
        $order = app(PaymentService::class)->createOrder($customer, $this->package, [
            'billing_name' => 'Webhook Customer',
            'billing_email' => 'webhook@example.com',
            'billing_phone' => '9876500020',
            'billing_state' => 'KA',
        ]);
        $transaction = app(PaymentService::class)->initiateTransaction($order);

        $webhookSecret = config('services.razorpay.webhook_secret');
        $eventId = 'evt_test_unique_12345';
        $payloadArray = [
            'event_id' => $eventId,
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_hook_987',
                        'order_id' => $transaction->gateway_order_id,
                        'amount' => $order->total_amount_paise,
                    ],
                ],
            ],
        ];
        $payloadRaw = json_encode($payloadArray);
        $signature = hash_hmac('sha256', $payloadRaw, $webhookSecret);

        // 1. First webhook execution: processes successfully
        $response1 = $this->withHeaders([
            'X-Razorpay-Signature' => $signature,
        ])->postJson('/webhooks/razorpay', $payloadArray);

        $response1->assertStatus(200);
        $response1->assertJson(['status' => 'processed']);

        $order->refresh();
        $this->assertEquals(Order::STATUS_PAID, $order->status);

        // 2. Replay duplicate webhook: should be ignored idempotently without duplicate processing
        $response2 = $this->withHeaders([
            'X-Razorpay-Signature' => $signature,
        ])->postJson('/webhooks/razorpay', $payloadArray);

        $response2->assertStatus(200);
        $response2->assertJson(['status' => 'duplicate_ignored']);

        // Assert only ONE invoice exists for this order
        $this->assertEquals(1, Invoice::where('order_id', $order->id)->count());
    }

    /**
     * Test staff can view revenue pipeline and authorize customer refunds.
     */
    public function test_staff_can_view_billing_and_authorize_refund(): void
    {
        $customer = User::factory()->customer()->create();
        $caseManager = User::factory()->caseManager()->create();

        // Customer receives 403 on staff billing queue
        $this->actingAs($customer)->get('/admin/billing')->assertForbidden();

        $order = app(PaymentService::class)->createOrder($customer, $this->package, [
            'billing_name' => 'Refund Test User',
            'billing_email' => 'refund@example.com',
            'billing_phone' => '9876500030',
            'billing_state' => 'UP',
        ]);
        $order->update(['status' => Order::STATUS_PAID]);
        app(\App\Services\InvoiceService::class)->generateInvoice($order);

        // Staff can view queue
        $this->actingAs($caseManager)
            ->get('/admin/billing')
            ->assertStatus(200)
            ->assertSee($order->order_number);

        // Staff authorizes full refund
        $refundResponse = $this->actingAs($caseManager)->post("/admin/billing/orders/{$order->id}/refund", [
            'amount_paise' => $order->total_amount_paise,
            'reason' => 'Customer requested cancellation prior to ROC investigation.',
        ]);

        $refundResponse->assertRedirect(route('admin.billing.show', $order));

        $order->refresh();
        $this->assertEquals(Order::STATUS_REFUNDED, $order->status);

        $this->assertDatabaseHas('refunds', [
            'order_id' => $order->id,
            'amount_paise' => $order->total_amount_paise,
            'status' => Refund::STATUS_PROCESSED,
            'approved_by_user_id' => $caseManager->id,
        ]);
    }
}
