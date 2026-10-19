<x-layout.app title="Your Match Compatibility Results — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-10 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-1">
                        Compatibility Assessment Results
                    </span>
                    <h1 class="font-serif text-3xl font-bold text-ink-primary">
                        Found {{ $matches->count() }} Verified Matching Opportunities
                    </h1>
                </div>
                <a href="{{ route('assessment.index') }}" class="text-xs font-semibold text-ink-primary hover:underline">
                    &larr; Re-adjust Criteria
                </a>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Prominent Non-Guarantee Disclaimer -->
        <div class="mb-8">
            <x-ui.alert variant="info" title="How to Read Your Compatibility Score:">
                The Fit Score is an objective compatibility index between your self-reported capital/location inputs and the brand's audited disclosures. It does <strong>not</strong> represent a probability of application approval, business turnover, or commercial profit. The originating company retains sole discretion over all approvals.
            </x-ui.alert>
        </div>

        @if($matches->count() > 0)
            <div class="space-y-6">
                @foreach($matches as $match)
                    @php
                        $opp = $match['opportunity'];
                        $score = $match['fit_score'];
                    @endphp
                    <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 sm:p-8 hover:border-ink-primary transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 pb-6 border-b border-border-subtle">
                            <div class="space-y-2 max-w-2xl">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                                        {{ $opp->category->name }}
                                    </span>
                                    <x-ui.badge variant="verified" size="sm">
                                        Verified: {{ $opp->last_verified_at?->format('d M Y') }}
                                    </x-ui.badge>
                                </div>
                                <h2 class="font-serif text-2xl font-bold text-ink-primary">
                                    <a href="{{ route('opportunities.show', $opp->slug) }}" class="hover:underline">
                                        {{ $opp->title }}
                                    </a>
                                </h2>
                                <p class="text-xs text-ink-muted">
                                    Brand: <strong class="text-ink-secondary">{{ $opp->company->brand_name }}</strong>
                                    &bull; Model: <span class="font-mono">{{ $opp->business_model }}</span>
                                    &bull; Outlay: <strong class="text-ink-primary font-mono">{{ $opp->formattedInvestmentRange() }}</strong>
                                </p>
                            </div>

                            <!-- Fit Score Indicator -->
                            <div class="flex items-center lg:flex-col lg:items-end gap-3 flex-shrink-0">
                                <div class="inline-flex items-baseline gap-1 px-3 py-1.5 bg-accent-green-bg border border-status-verified-border rounded-sm">
                                    <span class="font-mono text-2xl font-bold text-status-verified-text">{{ $score }}</span>
                                    <span class="text-xs font-medium text-status-verified-text">/100 Fit Score</span>
                                </div>
                                <span class="text-[11px] text-ink-muted">Based on 5 Feasibility Weights</span>
                            </div>
                        </div>

                        <!-- Match Rationale & Caveats -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6 text-xs leading-relaxed">
                            <!-- Explainable Reasons -->
                            <div>
                                <h3 class="font-semibold text-ink-primary uppercase tracking-wider text-[11px] mb-3 flex items-center gap-1.5">
                                    <span class="text-status-verified-text font-bold">&check;</span> Verified Match Factors
                                </h3>
                                <ul class="space-y-2 text-ink-secondary">
                                    @foreach($match['reasons'] as $reason)
                                        <li class="flex items-start gap-2">
                                            <span class="text-status-verified-text">&bull;</span>
                                            <span>{{ $reason }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Important Caveats -->
                            <div>
                                <h3 class="font-semibold text-ink-primary uppercase tracking-wider text-[11px] mb-3 flex items-center gap-1.5">
                                    <span class="text-status-caveat-text font-bold">&#9888;</span> Caveats &amp; Unknowns to Verify
                                </h3>
                                <ul class="space-y-2 text-ink-secondary">
                                    @forelse($match['caveats'] as $caveat)
                                        <li class="flex items-start gap-2">
                                            <span class="text-status-caveat-text">&bull;</span>
                                            <span>{{ $caveat }}</span>
                                        </li>
                                    @empty
                                        <li class="text-ink-muted italic">No material caveats detected on preliminary criteria.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Card Action Bar -->
                        <div class="pt-4 border-t border-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4">
                            <a href="{{ route('opportunities.show', $opp->slug) }}" class="text-xs font-semibold text-ink-primary hover:underline">
                                Inspect Itemized Financial Disclosures &rarr;
                            </a>
                            <x-ui.button href="{{ route('enquiry.create', ['opportunity' => $opp->slug]) }}" variant="primary" class="!py-2 !px-5 !text-xs w-full sm:w-auto">
                                Request Managed Coordination for this Brand
                            </x-ui.button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- No Matches -->
            <div class="p-12 text-center bg-surface-paper/40 border border-border-subtle rounded-sm">
                <div class="max-w-md mx-auto">
                    <h3 class="font-serif text-xl font-bold text-ink-primary mb-2">
                        No Opportunities Matched Your Exact Criteria
                    </h3>
                    <p class="text-sm text-ink-secondary mb-6 leading-relaxed">
                        Our hard filters excluded listings where minimum investment exceeds your budget or where territory intake is inactive.
                    </p>
                    <x-ui.button href="{{ route('assessment.index') }}" variant="secondary">
                        Adjust Budget or Location
                    </x-ui.button>
                </div>
            </div>
        @endif
    </div>
</x-layout.app>
