<x-layout.app title="Order Dossier #{{ $order->order_number }} — Staff Operations" robots="noindex, nofollow">
    <section class="bg-surface-paper border-b border-border-subtle py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('admin.billing.index') }}" class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted hover:text-ink-primary">
                            &larr; Billing Pipeline
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
                            <a href="{{ route('customer.invoices.show', $order->invoice) }}" target="_blank" class="px-3 py-1.5 bg-ink-primary text-white text-xs font-semibold rounded-xs hover:bg-accent-navy-light shadow-sm">
                                View Tax Invoice &rarr;
                            </a>
                        @endif
                    @elseif ($order->isPending())
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Pending Payment
                        </span>
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
        @if (session('error'))
            <x-ui.alert variant="danger">
                {{ session('error') }}
            </x-ui.alert>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-xs">
            <!-- Left 2 Cols: Order & Transaction Dossier -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h2 class="font-serif text-base font-bold text-ink-primary border-b border-border-subtle pb-3">
                        Commercial Order Specifications
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Buyer Entity &amp; User</span>
                            <span class="font-medium text-ink-primary text-sm">{{ $order->billing_name }}</span>
                            <span class="text-ink-secondary block">{{ $order->user->name }} (User #{{ $order->user_id }})</span>
                            <span class="font-mono text-ink-muted">{{ $order->billing_email }} &bull; {{ $order->billing_phone }}</span>
                        </div>

                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Tax Location (Place of Supply)</span>
                            <span class="font-mono text-ink-primary font-bold">{{ $order->billing_state }} {{ $order->billing_city ? '— '.$order->billing_city : '' }}</span>
                            @if ($order->billing_gstin)
                                <div class="font-mono text-accent-green font-bold text-[11px] mt-0.5">GSTIN: {{ $order->billing_gstin }}</div>
                            @else
                                <div class="text-[10px] text-ink-muted mt-0.5">Unregistered B2C Consumer</div>
                            @endif
                        </div>
                    </div>

                    @if ($order->enquiry)
                        <div class="pt-3 border-t border-border-subtle flex items-center justify-between">
                            <div>
                                <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Associated Case Docket</span>
                                <span class="font-mono text-ink-primary font-bold">{{ $order->enquiry->reference_code }}</span>
                            </div>
                            <a href="{{ route('admin.cases.show', $order->enquiry) }}" class="text-accent-navy-light hover:underline font-medium">
                                Open Case Workspace &rarr;
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Transaction Attempts -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary border-b border-border-subtle pb-3">
                        Gateway Transactions &amp; Signature Logs
                    </h3>

                    @if ($order->transactions->count() > 0)
                        <div class="divide-y divide-border-subtle">
                            @foreach ($order->transactions as $txn)
                                <div class="py-3 space-y-1">
                                    <div class="flex items-center justify-between">
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
                                        <span class="font-mono font-bold text-ink-primary">{{ $txn->formatted_amount }}</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-ink-muted">
                                        <div>Gateway Order ID: <span class="font-mono text-ink-primary">{{ $txn->gateway_order_id }}</span></div>
                                        <div>Payment ID: <span class="font-mono text-ink-primary">{{ $txn->gateway_payment_id ?? 'None' }}</span></div>
                                    </div>
                                    @if ($txn->failure_reason)
                                        <div class="text-[11px] text-red-700 font-medium">Failure Reason: {{ $txn->failure_reason }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-ink-muted">No transactions generated.</p>
                    @endif
                </div>
            </div>

            <!-- Right Col: Financials & Staff Refund Authorization -->
            <div class="space-y-6">
                <!-- Financials Card -->
                <div class="bg-surface-paper border border-border-strong rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary">
                        Billing Breakdown
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
                            <span>Total Payable:</span>
                            <span class="font-mono text-accent-green text-base">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>

                <!-- Staff Authorized Refund Box -->
                @if ($order->isPaid())
                    <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                        <h3 class="font-serif text-base font-bold text-ink-primary">
                            Authorize Customer Refund
                        </h3>
                        <p class="text-[11px] text-ink-muted leading-relaxed">
                            Refunds must be authorized by staff in accordance with the published cancellation policy. AI cannot authorize refunds.
                        </p>

                        <form method="POST" action="{{ route('admin.billing.refund', $order) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label for="amount_paise" class="block font-semibold uppercase tracking-wider text-ink-muted text-[10px] mb-1">
                                    Refund Amount (Paise) <span class="text-red-600">*</span>
                                </label>
                                <input type="number" id="amount_paise" name="amount_paise" value="{{ $order->total_amount_paise }}" max="{{ $order->total_amount_paise }}" required class="w-full px-3 py-1.5 bg-surface-paper border border-border-strong rounded-xs font-mono text-ink-primary">
                                <span class="text-[10px] text-ink-muted mt-0.5 block">Full refund: {{ $order->total_amount_paise }} paise ({{ $order->formatted_total }})</span>
                            </div>

                            <div>
                                <label for="reason" class="block font-semibold uppercase tracking-wider text-ink-muted text-[10px] mb-1">
                                    Statutory Justification / Audit Reason <span class="text-red-600">*</span>
                                </label>
                                <textarea id="reason" name="reason" rows="2" required placeholder="Reason for refund (e.g. Cancelled within 24h prior to docket inception)" class="w-full px-3 py-1.5 bg-surface-paper border border-border-strong rounded-xs text-ink-primary text-xs"></textarea>
                            </div>

                            <button type="submit" class="w-full py-2 bg-red-700 text-white rounded-xs font-semibold hover:bg-red-800 transition-colors shadow-sm cursor-pointer text-xs">
                                Authorize Refund &rarr;
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout.app>
