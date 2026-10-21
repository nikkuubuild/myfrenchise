<x-layout.app title="Tax Invoice {{ $invoice->invoice_number }} — Franchise Dekho" robots="noindex, nofollow">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Action Toolbar (hidden on print) -->
        <div class="mb-6 flex items-center justify-between print:hidden">
            <a href="{{ route('customer.billing.index') }}" class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted hover:text-ink-primary">
                &larr; Back to Invoices
            </a>

            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-ink-primary text-white text-xs font-semibold rounded-xs hover:bg-accent-navy-light cursor-pointer shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print / Save PDF
                </button>
            </div>
        </div>

        <!-- Official Tax Invoice Document -->
        <div class="bg-surface-canvas border border-border-strong rounded-sm p-8 sm:p-12 shadow-sm text-ink-primary space-y-8 print:border-none print:shadow-none print:p-0">
            <!-- Header Banner -->
            <div class="border-b border-border-subtle pb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <span class="font-serif font-bold text-2xl text-ink-primary block">Franchise Dekho</span>
                    <span class="text-xs font-mono text-ink-muted block mt-0.5">Managed Business Opportunity Platform</span>
                </div>

                <div class="text-left sm:text-right">
                    <h1 class="font-serif text-xl sm:text-2xl font-bold uppercase tracking-wider text-ink-primary">
                        GST TAX INVOICE
                    </h1>
                    <div class="font-mono text-xs text-ink-secondary mt-1 space-y-0.5">
                        <div><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</div>
                        <div><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</div>
                        <div><strong>Order Reference:</strong> {{ $order->order_number }}</div>
                        <div><strong>Financial Year:</strong> {{ $invoice->financial_year }}</div>
                    </div>
                </div>
            </div>

            <!-- Seller & Buyer Entity Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs border-b border-border-subtle pb-6">
                <!-- Supplier / Seller Details -->
                <div class="space-y-1.5">
                    <span class="font-semibold uppercase tracking-wider text-ink-muted text-[10px] block">
                        Service Provider (Supplier)
                    </span>
                    <div class="font-bold text-sm text-ink-primary">{{ \App\Models\Invoice::SELLER_LEGAL_NAME }}</div>
                    <div class="text-ink-secondary leading-relaxed">{{ \App\Models\Invoice::SELLER_ADDRESS }}</div>
                    <div class="font-mono pt-1"><strong>GSTIN:</strong> {{ \App\Models\Invoice::SELLER_GSTIN }}</div>
                    <div class="font-mono"><strong>State:</strong> Haryana (State Code: 06)</div>
                </div>

                <!-- Recipient / Buyer Details -->
                <div class="space-y-1.5 sm:text-right">
                    <span class="font-semibold uppercase tracking-wider text-ink-muted text-[10px] block">
                        Billed To (Recipient / Payer)
                    </span>
                    <div class="font-bold text-sm text-ink-primary">{{ $order->billing_name }}</div>
                    <div class="text-ink-secondary">{{ $order->billing_city ? $order->billing_city.', ' : '' }}{{ $order->billing_state }}</div>
                    <div class="text-ink-muted font-mono">{{ $order->billing_email }} &bull; {{ $order->billing_phone }}</div>
                    <div class="font-mono pt-1">
                        <strong>Place of Supply (POS):</strong> {{ $invoice->customer_state }}
                    </div>
                    @if ($order->billing_gstin)
                        <div class="font-mono text-accent-green font-bold">
                            <strong>Recipient GSTIN:</strong> {{ $order->billing_gstin }}
                        </div>
                    @else
                        <div class="text-[10px] text-ink-muted">Consumer / Unregistered Person (B2C)</div>
                    @endif
                </div>
            </div>

            <!-- Tax Invoice Line Items Table -->
            <div class="border border-border-subtle rounded-xs overflow-hidden">
                <table class="min-w-full divide-y divide-border-subtle text-xs">
                    <thead class="bg-surface-paper text-ink-primary font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Description of Service</th>
                            <th class="px-4 py-3 text-center">SAC Code</th>
                            <th class="px-4 py-3 text-right">Taxable Value</th>
                            @if ($invoice->is_interstate)
                                <th class="px-4 py-3 text-right">IGST (18%)</th>
                            @else
                                <th class="px-4 py-3 text-right">CGST (9%)</th>
                                <th class="px-4 py-3 text-right">SGST (9%)</th>
                            @endif
                            <th class="px-4 py-3 text-right">Total (INR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle text-ink-secondary font-mono">
                        <tr>
                            <td class="px-4 py-3">1</td>
                            <td class="px-4 py-3 font-sans text-ink-primary">
                                <div class="font-semibold">{{ $order->servicePackage->name }}</div>
                                <div class="text-[11px] text-ink-muted">Independent statutory due diligence, corporate filing check &amp; application dossier coordination</div>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $invoice->sac_code }}</td>
                            <td class="px-4 py-3 text-right">{{ $invoice->formatted_subtotal }}</td>
                            @if ($invoice->is_interstate)
                                <td class="px-4 py-3 text-right">{{ $invoice->formatted_igst }}</td>
                            @else
                                <td class="px-4 py-3 text-right">{{ $invoice->formatted_cgst }}</td>
                                <td class="px-4 py-3 text-right">{{ $invoice->formatted_sgst }}</td>
                            @endif
                            <td class="px-4 py-3 text-right font-bold text-ink-primary">{{ $invoice->formatted_total }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-surface-paper text-xs divide-y divide-border-subtle">
                        <tr>
                            <td colspan="{{ $invoice->is_interstate ? 4 : 5 }}" class="px-4 py-2 text-right font-semibold text-ink-secondary font-sans">
                                Total Taxable Amount:
                            </td>
                            <td class="px-4 py-2 text-right font-mono font-bold text-ink-primary">
                                {{ $invoice->formatted_subtotal }}
                            </td>
                        </tr>
                        @if ($invoice->is_interstate)
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right font-semibold text-ink-secondary font-sans">
                                    Integrated GST (IGST 18%):
                                </td>
                                <td class="px-4 py-2 text-right font-mono font-bold text-ink-primary">
                                    {{ $invoice->formatted_igst }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-right font-semibold text-ink-secondary font-sans">
                                    Central GST (CGST 9%):
                                </td>
                                <td class="px-4 py-2 text-right font-mono font-bold text-ink-primary">
                                    {{ $invoice->formatted_cgst }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-right font-semibold text-ink-secondary font-sans">
                                    State GST (SGST 9%):
                                </td>
                                <td class="px-4 py-2 text-right font-mono font-bold text-ink-primary">
                                    {{ $invoice->formatted_sgst }}
                                </td>
                            </tr>
                        @endif
                        <tr class="bg-surface-canvas border-t-2 border-border-strong font-bold">
                            <td colspan="{{ $invoice->is_interstate ? 4 : 5 }}" class="px-4 py-3 text-right text-sm text-ink-primary font-sans">
                                Invoice Total (INR):
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-base text-accent-green">
                                {{ $invoice->formatted_total }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Statutory Declarations and Notes -->
            <div class="border-t border-border-subtle pt-6 grid grid-cols-1 sm:grid-cols-2 gap-6 text-[11px] text-ink-muted leading-relaxed">
                <div class="space-y-1">
                    <span class="font-semibold text-ink-secondary uppercase text-[10px] block">Statutory Declaration</span>
                    <p>
                        We declare that this invoice shows the actual price of the services described and that all particulars are true and correct. Taxes have been charged as per the provisions of the Central Goods and Services Tax Act, 2017 and Integrated Goods and Services Tax Act, 2017.
                    </p>
                </div>

                <div class="sm:text-right space-y-1">
                    <span class="font-semibold text-ink-secondary uppercase text-[10px] block">Authentication Note</span>
                    <p>
                        This is a computer-generated tax invoice issued through Franchise Dekho Platform Operations. In accordance with Rule 46 of CGST Rules 2017, it does not require a physical signature.
                    </p>
                    <div class="pt-2 font-mono text-ink-secondary">
                        Payment Status: <strong>PAID IN FULL</strong> &bull; Bank Settlement Verified
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
