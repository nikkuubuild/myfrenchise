<x-layout.app title="Transparent Fee Checkout — {{ $package->name }} — Franchise Dekho" robots="noindex, nofollow">
    <section class="bg-surface-paper border-b border-border-subtle py-10 md:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                    Transparent Pricing &bull; No Hidden Surcharges
                </span>
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink-primary mb-3">
                    {{ $package->name }}
                </h1>
                <p class="text-sm text-ink-secondary leading-relaxed">
                    {{ $package->summary }}
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left 2 Cols: Deliverables & Billing Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Inclusions & Exclusions Dossier -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 sm:p-8 space-y-6">
                    <h2 class="font-serif text-lg font-bold text-ink-primary border-b border-border-subtle pb-3">
                        Scope of Service &amp; Deliverables
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <!-- What is included -->
                        <div class="space-y-3">
                            <h3 class="font-semibold text-accent-green uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                                <span>&check;</span> Service Inclusions
                            </h3>
                            <ul class="space-y-2 text-ink-secondary">
                                @foreach ($package->inclusions as $inc)
                                    <li class="flex items-start gap-2">
                                        <span class="text-accent-green font-bold">&bull;</span>
                                        <span>{{ $inc }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- What is NOT included -->
                        <div class="space-y-3">
                            <h3 class="font-semibold text-status-rejected-text uppercase tracking-wider text-[11px] flex items-center gap-1.5">
                                <span>&times;</span> Critical Limitations &amp; Exclusions
                            </h3>
                            <ul class="space-y-2 text-ink-secondary">
                                @foreach ($package->exclusions as $exc)
                                    <li class="flex items-start gap-2">
                                        <span class="text-status-rejected-text font-bold">&bull;</span>
                                        <span>{{ $exc }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Cancellation & Refund Policy -->
                    <div class="p-4 bg-surface-paper border border-border-subtle rounded-xs space-y-1.5 text-xs">
                        <span class="font-semibold text-ink-primary uppercase tracking-wider text-[10px] block">
                            Cancellation &amp; Refund Policy
                        </span>
                        <p class="text-ink-secondary leading-relaxed">
                            {{ $package->cancellation_policy }}
                        </p>
                    </div>
                </div>

                <!-- Billing Information Form -->
                <div class="bg-surface-canvas border border-border-strong rounded-sm p-6 sm:p-8 space-y-6">
                    <div class="border-b border-border-subtle pb-3">
                        <h2 class="font-serif text-lg font-bold text-ink-primary">
                            Tax Invoice &amp; Payer Coordinates
                        </h2>
                        <p class="text-xs text-ink-muted mt-0.5">
                            Required to generate your statutory Indian GST Tax Invoice (SAC {{ $package->sac_code }}).
                        </p>
                    </div>

                    @if ($errors->any())
                        <x-ui.alert variant="danger">
                            <ul class="list-disc list-inside text-xs space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    @endif

                    <form method="POST" action="{{ route('checkout.process', $package->slug) }}" class="space-y-5 text-xs">
                        @csrf

                        @if ($targetEnquiry)
                            <input type="hidden" name="enquiry_id" value="{{ $targetEnquiry->id }}">
                            <div class="p-3 bg-surface-paper border border-border-subtle rounded-xs flex items-center justify-between text-xs">
                                <span>Linked to Case Dossier: <strong>{{ $targetEnquiry->reference_code }}</strong></span>
                                <span class="font-mono text-ink-muted">Territory: {{ $targetEnquiry->target_city }}, {{ $targetEnquiry->target_state }}</span>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="billing_name" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Legal Name / Entity <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="billing_name" name="billing_name" value="{{ old('billing_name', $user?->name) }}" required placeholder="Payer Full Name or Registered Firm Name" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary">
                            </div>

                            <div>
                                <label for="billing_email" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Billing Email <span class="text-red-600">*</span>
                                </label>
                                <input type="email" id="billing_email" name="billing_email" value="{{ old('billing_email', $user?->email) }}" required placeholder="Invoice receipt destination" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="billing_phone" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Mobile Phone <span class="text-red-600">*</span>
                                </label>
                                <input type="tel" id="billing_phone" name="billing_phone" value="{{ old('billing_phone', $user?->phone) }}" required placeholder="+91 98765 43210" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary">
                            </div>

                            <div>
                                <label for="billing_state" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    State (GST Place of Supply) <span class="text-red-600">*</span>
                                </label>
                                <select id="billing_state" name="billing_state" required class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary">
                                    <option value="">Select State...</option>
                                    @php
                                        $states = [
                                            'HR' => 'Haryana (Intra-state CGST+SGST)',
                                            'DL' => 'Delhi NCR (IGST)',
                                            'MH' => 'Maharashtra (IGST)',
                                            'KA' => 'Karnataka (IGST)',
                                            'UP' => 'Uttar Pradesh (IGST)',
                                            'TN' => 'Tamil Nadu (IGST)',
                                            'GJ' => 'Gujarat (IGST)',
                                            'WB' => 'West Bengal (IGST)',
                                            'RJ' => 'Rajasthan (IGST)',
                                            'TG' => 'Telangana (IGST)',
                                            'PB' => 'Punjab (IGST)',
                                            'MP' => 'Madhya Pradesh (IGST)',
                                        ];
                                    @endphp
                                    @foreach ($states as $code => $label)
                                        <option value="{{ $code }}" {{ old('billing_state', $user?->state ?? 'MH') === $code ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="billing_city" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    City
                                </label>
                                <input type="text" id="billing_city" name="billing_city" value="{{ old('billing_city', $user?->city) }}" placeholder="e.g. Pune, Gurugram" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                            </div>
                        </div>

                        <div>
                            <label for="billing_gstin" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                GSTIN for Business Input Tax Credit <span class="text-ink-muted font-normal">(Optional B2B)</span>
                            </label>
                            <input type="text" id="billing_gstin" name="billing_gstin" value="{{ old('billing_gstin') }}" placeholder="15-character GSTIN (e.g. 27AAAAA0000A1Z5)" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary font-mono uppercase">
                        </div>

                        <div class="pt-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="statutory_declaration" value="1" required class="mt-0.5 rounded-xs border-border-strong text-ink-primary focus:ring-ink-primary">
                                <span class="text-xs text-ink-secondary leading-relaxed">
                                    I confirm that this payment is for independent research and managed coordination services delivered by Franchise Dekho. I understand that Franchise Dekho is not a franchisor, does not sell licenses, and does not guarantee brand acceptance, business profitability, or commercial returns.
                                </span>
                            </label>
                        </div>

                        <div class="pt-4 border-t border-border-subtle flex items-center justify-between">
                            <span class="text-[11px] text-ink-muted">
                                Protected by 256-Bit SSL &bull; Instant GST Tax Invoice
                            </span>
                            <button type="submit" class="px-6 py-2.5 bg-ink-primary text-white text-xs font-semibold rounded-sm hover:bg-accent-navy-light transition-colors shadow-sm cursor-pointer">
                                Proceed to Payment (₹{{ number_format($totalPaise / 100, 2) }}) &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Col: Fee Summary Sidebar -->
            <div class="space-y-6">
                <div class="bg-surface-paper border border-border-strong rounded-sm p-6 space-y-4">
                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block">
                        Order Summary
                    </span>
                    <h3 class="font-serif text-lg font-bold text-ink-primary">
                        Fee Breakdown
                    </h3>

                    <div class="divide-y divide-border-subtle text-xs space-y-3 pt-2">
                        <div class="flex items-center justify-between text-ink-secondary pt-2">
                            <span>Professional Service Fee:</span>
                            <strong class="text-ink-primary font-mono">₹{{ number_format($basePaise / 100, 2) }}</strong>
                        </div>
                        <div class="flex items-center justify-between text-ink-secondary pt-3">
                            <span>GST (18% &bull; SAC {{ $package->sac_code }}):</span>
                            <strong class="text-ink-primary font-mono">₹{{ number_format($taxPaise / 100, 2) }}</strong>
                        </div>
                        <div class="flex items-center justify-between text-ink-primary text-sm pt-3 font-bold border-t border-border-strong">
                            <span>Total Payable:</span>
                            <span class="text-accent-green font-mono text-base">₹{{ number_format($totalPaise / 100, 2) }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-border-subtle space-y-2 text-[11px] text-ink-muted">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Official Tax Invoice issued immediately on receipt</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>Direct bank and UPI settlement via verified gateway</span>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-canvas p-5 border border-border-subtle rounded-sm text-xs text-ink-muted space-y-2">
                    <span class="font-semibold text-ink-secondary uppercase text-[10px] block">No Unauthorized Cash Collections</span>
                    <p class="leading-relaxed">
                        Franchise Dekho strictly prohibits cash collections or offline kickbacks. All official fees are collected electronically with digital tax invoices.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
