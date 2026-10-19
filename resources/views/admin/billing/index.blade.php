<x-layout.app title="Billing &amp; Revenue Pipeline — Staff Operations">
    <section class="bg-surface-paper border-b border-border-subtle py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                            Operations &bull; Commercial Revenue &amp; GST Invoicing
                        </span>
                        <x-ui.badge variant="verified" size="sm">Staff Queue</x-ui.badge>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        Commercial Orders &amp; Billing Pipeline
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.cases.index') }}" class="text-xs font-medium px-3 py-1.5 bg-surface-canvas border border-border-strong rounded-sm text-ink-primary hover:border-ink-primary">
                        &larr; Case Pipeline
                    </a>
                    <a href="{{ route('admin.billing.index', ['status' => 'paid']) }}" class="text-xs font-medium px-3 py-1.5 bg-ink-primary text-white rounded-sm hover:bg-accent-navy-light shadow-sm">
                        Paid Orders ({{ $metrics['paid_orders'] }})
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if (session('status'))
            <x-ui.alert variant="success">
                {{ session('status') }}
            </x-ui.alert>
        @endif

        <!-- Revenue Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted block mb-1">Total Revenue</span>
                <span class="font-serif text-2xl font-bold text-accent-green">
                    ₹{{ number_format($metrics['total_revenue_paise'] / 100, 2) }}
                </span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted block mb-1">Total Orders</span>
                <span class="font-serif text-2xl font-bold text-ink-primary">{{ $metrics['total_orders'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700 block mb-1">Paid &amp; Invoiced</span>
                <span class="font-serif text-2xl font-bold text-emerald-700">{{ $metrics['paid_orders'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-700 block mb-1">Pending Payment</span>
                <span class="font-serif text-2xl font-bold text-amber-700">{{ $metrics['pending_orders'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-purple-700 block mb-1">Refunded</span>
                <span class="font-serif text-2xl font-bold text-purple-700">{{ $metrics['refunded_orders'] }}</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
            <form method="GET" action="{{ route('admin.billing.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end text-xs">
                <div>
                    <label for="q" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Search Orders</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Order #, buyer name, or email..." class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                </div>

                <div>
                    <label for="status" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Payment Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                        <option value="all">All Statuses</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 lg:col-span-2">
                    <button type="submit" class="px-5 py-2 bg-ink-primary text-white rounded-xs font-semibold hover:bg-accent-navy-light cursor-pointer">
                        Filter Orders
                    </button>
                    @if (request()->hasAny(['q', 'status']))
                        <a href="{{ route('admin.billing.index') }}" class="px-3 py-2 bg-surface-paper border border-border-subtle rounded-xs text-ink-muted hover:text-ink-primary">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-surface-canvas border border-border-subtle rounded-sm overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border-subtle text-xs">
                    <thead class="bg-surface-paper text-ink-primary font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Order &amp; Date</th>
                            <th class="px-4 py-3 text-left">Buyer / Entity</th>
                            <th class="px-4 py-3 text-left">Service Package</th>
                            <th class="px-4 py-3 text-left">Amount (INR)</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">GST Invoice</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-surface-paper/40 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-mono font-bold text-ink-primary">{{ $order->order_number }}</div>
                                    <div class="text-[11px] text-ink-muted">{{ $order->created_at->format('d M Y - H:i') }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-ink-primary">{{ $order->billing_name }}</div>
                                    <div class="text-[11px] text-ink-muted font-mono">{{ $order->billing_email }}</div>
                                    <div class="text-[10px] text-ink-muted">POS: {{ $order->billing_state }} {{ $order->billing_gstin ? '• GSTIN' : '' }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-[11px] font-medium text-ink-primary">{{ $order->servicePackage->name }}</div>
                                    @if ($order->enquiry)
                                        <div class="text-[10px] text-accent-navy-light font-mono">Case: {{ $order->enquiry->reference_code }}</div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap font-mono font-bold text-ink-primary">
                                    {{ $order->formatted_total }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($order->status === 'paid')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Paid
                                        </span>
                                    @elseif ($order->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Pending
                                        </span>
                                    @elseif ($order->status === 'refunded')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-purple-100 text-purple-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Refunded
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Failed
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap font-mono text-[11px]">
                                    @if ($order->invoice)
                                        <a href="{{ route('customer.invoices.show', $order->invoice) }}" target="_blank" class="text-accent-green hover:underline font-semibold">
                                            {{ $order->invoice->invoice_number }}
                                        </a>
                                    @else
                                        <span class="text-ink-muted text-[10px]">Uninvoiced</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.billing.show', $order) }}" class="inline-flex items-center px-3 py-1 bg-ink-primary text-white text-xs font-medium rounded-xs hover:bg-accent-navy-light transition-colors">
                                        Review &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-ink-muted">
                                    No commercial orders match the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-4 border-t border-border-subtle bg-surface-paper">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout.app>
