<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    /**
     * Display list of customer orders and invoices.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $orders = $user->orders()
            ->with(['servicePackage', 'invoice', 'latestTransaction'])
            ->latest()
            ->get();

        return view('customer.billing.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Display order summary, transaction records, and status.
     */
    public function showOrder(Request $request, Order $order): View
    {
        if ($order->user_id !== $request->user()->id && ! $request->user()->isStaff()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['servicePackage', 'invoice', 'transactions', 'refunds', 'enquiry']);

        return view('customer.billing.order', [
            'order' => $order,
        ]);
    }

    /**
     * Display printable/downloadable statutory GST Tax Invoice.
     */
    public function showInvoice(Request $request, Invoice $invoice): View
    {
        $order = $invoice->order;

        if ($order->user_id !== $request->user()->id && ! $request->user()->isStaff()) {
            abort(403, 'Unauthorized access to this tax invoice.');
        }

        $invoice->load(['order.servicePackage', 'order.user']);

        return view('customer.billing.invoice', [
            'invoice' => $invoice,
            'order' => $order,
        ]);
    }
}
