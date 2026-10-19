<x-layout.app title="Order #{{ $order->order_number }} — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-8 md:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('customer.billing.index') }}" class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted hover:text-ink-primary">
                            &larr; Billing History
                        </a>
                        <span class="text-xs text-ink-muted">&bull;</span>
                        <span class="font-mono text-xs font-bold text-ink-primary">{{ $order->order_number }}</span>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        {{ $order->servicePackage->name }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    @if ($order->isPaid())
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Paid
                        </span>
                        @if ($order->invoice)
                            <a href="{{ route('customer.invoices.show', $order->invoice) }}" class="px-3 py-1.5 bg-ink-primary text-white text-xs font-semibold rounded-xs hover:bg-accent-navy-light shadow-sm">
                                View Tax Invoice &rarr;
                            </a>
                        @endif
                    @elseif ($order->isPending())
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Pending Payment
                        </span>
                        <a href="{{ route('checkout.pay', $order) }}" class="px-4 py-1.5 bg-accent-green text-white text-xs font-semibold rounded-xs hover:opacity-90 shadow-sm">
                            Complete Payment &rarr;
                        </a>
                    @elseif ($order->isRefunded())
                        <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Refunded
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if (session('status'))
            <x-ui.alert variant="success">
                {{ session('status') }}
            </x-ui.alert>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-xs">
            <!-- Left 2 Cols: Order & Deliverables Breakdown -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h2 class="font-serif text-base font-bold text-ink-primary border-b border-border-subtle pb-3">
                        Order Details &amp; Disclosures
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Order Number</span>
                            <span class="font-mono text-ink-primary font-bold">{{ $order->order_number }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Placed On</span>
                            <span class="text-ink-primary">{{ $order->created_at->format('d M Y - H:i') }} IST</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Billed To</span>
                            <span class="text-ink-primary font-medium">{{ $order->billing_name }} ({{ $order->billing_phone }})</span>
                            <span class="text-ink-muted block font-mono">{{ $order->billing_email }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Place of Supply (State)</span>
                            <span class="font-mono text-ink-primary">{{ $order->billing_state }} {{ $order->billing_city ? '— '.$order->billing_city : '' }}</span>
                            @if ($order->billing_gstin)
                                <span class="font-mono text-ink-muted text-[10px] block">GSTIN: {{ $order->billing_gstin }}</span>
                            @endif
                        </div>
                    </div>

                    @if ($order->enquiry)
                        <div class="pt-3 border-t border-border-subtle">
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Associated Case</span>
                            <a href="{{ route('customer.cases.documents.show', $order->enquiry) }}" class="text-accent-green hover:underline font-mono font-semibold">
                                {{ $order->enquiry->reference_code }} &rarr;
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Transaction Attempts -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary border-b border-border-subtle pb-3">
                        Payment &amp; Reconciliation History
                    </h3>

                    @if ($order->transactions->count() > 0)
                        <div class="divide-y divide-border-subtle">
                            @foreach ($order->transactions as $txn)
                                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-ink-primary">{{ $txn->transaction_reference }}</span>
                                            @if ($txn->status === 'captured')
                                                <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 text-[10px] font-semibold uppercase rounded-xs">Captured</span>
                                            @elseif ($txn->status === 'initiated')
                                                <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 text-[10px] font-semibold uppercase rounded-xs">Initiated</span>
                                            @else
                                                <span class="px-1.5 py-0.2 bg-red-100 text-red-800 text-[10px] font-semibold uppercase rounded-xs">Failed</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-ink-muted mt-0.5">
                                            Gateway: {{ ucfirst($txn->gateway) }} &bull; Method: {{ strtoupper($txn->payment_method ?? 'N/A') }}
                                            @if ($txn->gateway_payment_id)
                                                &bull; Payment ID: <span class="font-mono text-ink-primary">{{ $txn->gateway_payment_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono font-bold text-ink-primary">{{ $txn->formatted_amount }}</span>
                                        <span class="text-[10px] text-ink-muted block">{{ $txn->created_at->format('d M - H:i') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-ink-muted">No transaction records generated yet.</p>
                    @endif
                </div>

                <!-- Refunds if any -->
                @if ($order->refunds->count() > 0)
                    <div class="bg-purple-50/50 border border-purple-200 rounded-sm p-6 space-y-3">
                        <h3 class="font-serif text-base font-bold text-purple-950">
                            Processed Refunds
                        </h3>
                        @foreach ($order->refunds as $rfnd)
                            <div class="p-3 bg-surface-canvas border border-purple-200 rounded-xs flex items-center justify-between">
                                <div>
                                    <span class="font-mono font-bold text-purple-900">{{ $rfnd->refund_reference }}</span>
                                    <p class="text-xs text-purple-950 mt-0.5">Reason: {{ $rfnd->reason }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-purple-900">{{ $rfnd->formatted_amount }}</span>
                                    <span class="text-[10px] text-purple-700 block">{{ $rfnd->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Col: Financials -->
            <div class="space-y-6">
                <div class="bg-surface-paper border border-border-strong rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary">
                        Financial Summary
                    </h3>

                    <div class="divide-y divide-border-subtle space-y-2 text-xs">
                        <div class="flex items-center justify-between pt-2 text-ink-secondary">
                            <span>Service Fee:</span>
                            <span class="font-mono text-ink-primary">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 text-ink-secondary">
                            <span>GST (18%):</span>
                            <span class="font-mono text-ink-primary">{{ $order->formatted_tax }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-3 text-ink-primary font-bold text-sm border-t border-border-strong">
                            <span>Total Amount:</span>
                            <span class="font-mono text-accent-green text-base">{{ $order->formatted_total }}</span>
                        </div>
                    </div>

                    @if ($order->isPaid() && $order->invoice)
                        <div class="pt-4 border-t border-border-subtle">
                            <a href="{{ route('customer.invoices.show', $order->invoice) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-ink-primary text-white rounded-xs font-semibold hover:bg-accent-navy-light shadow-sm">
                                Download Tax Invoice &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
