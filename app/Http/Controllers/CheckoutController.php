<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\ServicePackage;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show transparent checkout and fee disclosure screen.
     */
    public function show(Request $request, ServicePackage $servicePackage): View
    {
        $user = $request->user();
        $targetEnquiry = null;

        if ($request->filled('enquiry')) {
            $targetEnquiry = Enquiry::where('reference_code', $request->input('enquiry'))
                ->orWhere('id', $request->input('enquiry'))
                ->first();
        }

        $basePaise = $servicePackage->base_price_paise;
        $taxPaise = $servicePackage->calculateTaxAmount($basePaise);
        $totalPaise = $basePaise + $taxPaise;

        return view('pages.checkout', [
            'package' => $servicePackage,
            'user' => $user,
            'targetEnquiry' => $targetEnquiry,
            'basePaise' => $basePaise,
            'taxPaise' => $taxPaise,
            'totalPaise' => $totalPaise,
        ]);
    }

    /**
     * Create order and prepare payment transaction.
     */
    public function process(Request $request, ServicePackage $servicePackage): RedirectResponse
    {
        $validated = $request->validate([
            'billing_name' => ['required', 'string', 'max:255'],
            'billing_email' => ['required', 'email', 'max:255'],
            'billing_phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{10,15}$/'],
            'billing_state' => ['required', 'string', 'size:2'],
            'billing_city' => ['nullable', 'string', 'max:100'],
            'billing_gstin' => ['nullable', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'enquiry_id' => ['nullable', 'exists:enquiries,id'],
            'statutory_declaration' => ['accepted'],
        ], [
            'statutory_declaration.accepted' => 'You must accept the terms acknowledging this fee covers professional coordination and does not guarantee brand license approval.',
            'billing_gstin.regex' => 'The GSTIN format is invalid. Please provide a valid 15-character GST identification number or leave blank.',
        ]);

        $user = $request->user();
        $enquiry = ! empty($validated['enquiry_id']) ? Enquiry::find($validated['enquiry_id']) : null;

        $order = $this->paymentService->createOrder($user, $servicePackage, $validated, $enquiry);
        $this->paymentService->initiateTransaction($order);

        return redirect()->route('checkout.pay', $order);
    }

    /**
     * Hosted payment transaction screen / gateway simulation.
     */
    public function pay(Request $request, Order $order): View|RedirectResponse
    {
        // Enforce ownership
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->isPaid()) {
            return redirect()->route('customer.orders.show', $order)
                ->with('status', 'This order has already been paid and processed.');
        }

        $transaction = $order->latestTransaction ?? $this->paymentService->initiateTransaction($order);
        $secret = config('services.razorpay.secret', 'test_razorpay_secret_key_abcdef');

        // Generate simulated valid payment credentials for test environment
        $mockPaymentId = 'pay_'.date('Ymd').'_'.strtolower(\Illuminate\Support\Str::random(10));
        $validSignature = hash_hmac('sha256', $transaction->gateway_order_id.'|'.$mockPaymentId, $secret);

        return view('pages.payment-gateway', [
            'order' => $order,
            'transaction' => $transaction,
            'mockPaymentId' => $mockPaymentId,
            'validSignature' => $validSignature,
        ]);
    }

    /**
     * Server-side callback for gateway payment authorization.
     */
    public function callback(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized payment authorization.');
        }

        $request->validate([
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
            'payment_method' => ['nullable', 'string', 'in:upi,netbanking,card,wallet'],
        ]);

        $transaction = $order->latestTransaction;
        if (! $transaction) {
            return redirect()->route('customer.orders.show', $order)
                ->with('error', 'No active payment transaction found for this order.');
        }

        $success = $this->paymentService->verifyAndCapture(
            $transaction,
            $request->input('razorpay_payment_id'),
            $request->input('razorpay_signature'),
            [
                'payment_method' => $request->input('payment_method', 'upi'),
                'bank' => $request->input('bank', 'HDFC'),
            ]
        );

        if (! $success) {
            return redirect()->route('checkout.pay', $order)
                ->with('error', 'Payment verification failed: Cryptographic signature mismatch. Your account was not charged.');
        }

        return redirect()->route('customer.orders.show', $order)
            ->with('status', 'Payment successful! Your statutory tax invoice #'.$order->invoice?->invoice_number.' has been issued.');
    }
}
