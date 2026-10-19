<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming signed Razorpay webhook notification.
     */
    public function handleRazorpay(Request $request): JsonResponse
    {
        $signature = $request->header('X-Razorpay-Signature', '');
        $payloadRaw = $request->getContent();

        $result = $this->paymentService->handleWebhook('razorpay', $payloadRaw, $signature);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['code'] ?? 200);
    }
}
