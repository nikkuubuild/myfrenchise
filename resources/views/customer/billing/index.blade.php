<x-layout.app title="Billing &amp; Tax Invoices — Customer Portal — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-8 md:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('dashboard') }}" class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted hover:text-ink-primary">
                            &larr; Return to Dashboard
                        </a>
                        <span class="text-xs text-ink-muted">&bull;</span>
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                            Commercial Billing &bull; Tax Invoices
                        </span>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        Orders &amp; Statutory GST Invoices
                    </h1>
                </div>

                <div>
                    <a href="{{ route('checkout.show', 'commercial-due-diligence') }}" class="px-4 py-2 bg-ink-primary text-white text-xs font-semibold rounded-sm hover:bg-accent-navy-light shadow-sm">
                        + New Due Diligence Order
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

        <div class="bg-surface-canvas border border-border-subtle rounded-sm overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border-subtle text-xs">
                    <thead class="bg-surface-paper text-ink-primary font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Order &amp; Package</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Amount (INR)</th>
                            <th class="px-4 py-3 text-left">Payment Status</th>
                            <th class="px-4 py-3 text-left">GST Tax Invoice</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-surface-paper/40 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="font-mono font-bold text-ink-primary">{{ $order->order_number }}</div>
                                    <div class="text-ink-secondary text-[11px]">{{ $order->servicePackage->name }}</div>
                                    @if ($order->enquiry)
                                        <div class="text-[10px] text-ink-muted">Case: {{ $order->enquiry->reference_code }}</div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-ink-secondary">
                                    {{ $order->created_at->format('d M Y - H:i') }}
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
                                            Pending Payment
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

                                <td class="px-4 py-3 whitespace-nowrap font-mono">
                                    @if ($order->invoice)
                                        <a href="{{ route('customer.invoices.show', $order->invoice) }}" class="text-accent-green hover:underline font-semibold flex items-center gap-1">
                                            <span>&darr;</span> {{ $order->invoice->invoice_number }}
                                        </a>
                                    @else
                                        <span class="text-ink-muted text-[10px]">Pending Payment</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-right space-x-2">
                                    <a href="{{ route('customer.orders.show', $order) }}" class="text-ink-primary hover:underline font-medium">
                                        View Details
                                    </a>
                                    @if ($order->isPending())
                                        <a href="{{ route('checkout.pay', $order) }}" class="px-2.5 py-1 bg-accent-green text-white rounded-xs font-semibold hover:opacity-90">
                                            Pay Now &rarr;
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-ink-muted">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <p>No billing or order history found on your account.</p>
                                        <a href="{{ route('checkout.show', 'commercial-due-diligence') }}" class="text-accent-green underline block font-semibold">
                                            Browse Available Due Diligence Packages &rarr;
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout.app>
