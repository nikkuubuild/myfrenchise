<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display staff operational billing and revenue pipeline.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'servicePackage', 'invoice', 'latestTransaction'])->latest();

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Search filter
        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('billing_name', 'like', "%{$q}%")
                    ->orWhere('billing_email', 'like', "%{$q}%")
                    ->orWhere('billing_phone', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $metrics = [
            'total_orders' => Order::count(),
            'paid_orders' => Order::where('status', Order::STATUS_PAID)->count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'refunded_orders' => Order::where('status', Order::STATUS_REFUNDED)->count(),
            'total_revenue_paise' => Order::where('status', Order::STATUS_PAID)->sum('total_amount_paise'),
        ];

        return view('admin.billing.index', [
            'orders' => $orders,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Display order detail dossier for staff review.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'servicePackage', 'invoice', 'transactions', 'refunds.approvedBy', 'enquiry']);

        return view('admin.billing.show', [
            'order' => $order,
        ]);
    }

    /**
     * Staff authorization of refund.
     */
    public function refund(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
            'amount_paise' => ['required', 'integer', 'min:100', 'max:'.$order->total_amount_paise],
        ]);

        try {
            $refund = $this->paymentService->processRefund(
                $order,
                $validated['amount_paise'],
                $validated['reason'],
                $request->user()
            );

            return redirect()->route('admin.billing.show', $order)
                ->with('status', 'Refund #'.$refund->refund_reference.' of '.$refund->formatted_amount.' successfully processed.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.billing.show', $order)
                ->with('error', 'Refund failed: '.$e->getMessage());
        }
    }
}
