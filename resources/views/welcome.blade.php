<x-layout.app 
    :canonical="url('/')"
    :schema="$schema"
>
    <!-- Hero Section on Warm Paper -->
    <section class="bg-surface-paper border-b border-border-subtle py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <!-- Eyebrow / Kicker -->
                <div class="inline-flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-accent-green"></span>
                    <span class="text-xs font-mono font-semibold uppercase tracking-widest text-ink-muted">
                        India-Wide Managed Opportunity Discovery
                    </span>
                </div>

                <!-- Main Headline -->
                <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-ink-primary tracking-tight leading-[1.15] mb-6">
                    Pursue Verified Business Opportunities with Coordinated Support.
                </h1>

                <!-- Subtitle -->
                <p class="text-lg md:text-xl text-ink-secondary leading-relaxed mb-10">
                    Franchise Dekho pairs independent regulatory research with dedicated application coordination. We verify legal and commercial facts, not marketing promises.
                </p>

                <!-- Search & Filter Formulation -->
                <form action="{{ route('opportunities.index') }}" method="GET" class="bg-surface-canvas p-4 sm:p-5 rounded-sm border border-border-subtle shadow-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <!-- Sector Filter -->
                        <div>
                            <label for="sector" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Sector
                            </label>
                            <select id="sector" name="sector" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                                <option value="">All Sectors</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Budget Filter -->
                        <div>
                            <label for="budget" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Investment Outlay
                            </label>
                            <select id="budget" name="budget" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                                <option value="">Any Budget</option>
                                <option value="under-5l">Under ₹5 Lakh</option>
                                <option value="5l-15l">₹5 Lakh - ₹15 Lakh</option>
                                <option value="15l-30l">₹15 Lakh - ₹30 Lakh</option>
                                <option value="30l-plus">₹30 Lakh+</option>
                            </select>
                        </div>

                        <!-- Location Filter -->
                        <div>
                            <label for="state" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Target State
                            </label>
                            <select id="state" name="state" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                                <option value="">All India</option>
                                <option value="UP">Uttar Pradesh</option>
                                <option value="MH">Maharashtra</option>
                                <option value="DL">Delhi NCR</option>
                                <option value="KA">Karnataka</option>
                                <option value="RJ">Rajasthan</option>
                                <option value="BR">Bihar</option>
                                <option value="MP">Madhya Pradesh</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2 border-t border-border-subtle">
                        <span class="text-xs text-ink-muted">
                            Every published listing displays legal entity checks & verified date.
                        </span>
                        <x-ui.button type="submit" variant="primary" class="w-full sm:w-auto">
                            Search Verified Opportunities
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Verified Opportunities Section -->
    @if(isset($featuredOpportunities) && $featuredOpportunities->count() > 0)
        <section class="py-16 border-b border-border-subtle">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8 pb-3 border-b border-border-subtle">
                    <div>
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-1">
                            Audited Intake
                        </span>
                        <h2 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                            Featured Verified Opportunities
                        </h2>
                    </div>
                    <a href="{{ route('opportunities.index') }}" class="text-sm font-semibold text-ink-primary hover:underline">
                        View All {{ $stats['total_opportunities'] ?? '' }} &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredOpportunities as $featured)
                        <x-opportunity.card :opportunity="$featured" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Curated Opportunity Categories Section -->
    <section class="py-16 md:py-20 border-b border-border-subtle">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 pb-4 border-b border-border-subtle">
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary mb-2">
                        Audited Sectors
                    </h2>
                    <p class="text-sm text-ink-muted">
                        Explore business categories actively monitored under our verification workflow.
                    </p>
                </div>
                <a href="{{ route('opportunities.index') }}" class="mt-4 sm:mt-0 text-sm font-medium text-ink-primary hover:underline inline-flex items-center gap-1">
                    View Catalog &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $cat)
                    <div class="p-6 bg-surface-canvas border border-border-subtle rounded-sm hover:border-ink-primary transition-colors flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                                    Sector {{ $cat->sort_order }}
                                </span>
                                @if(isset($cat->opportunities_count))
                                    <span class="text-xs font-mono text-ink-muted">
                                        {{ $cat->opportunities_count }} Verified
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-serif text-lg font-bold text-ink-primary mb-2">
                                <a href="{{ route('opportunities.index', ['sector' => $cat->slug]) }}" class="hover:underline">
                                    {{ $cat->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-ink-secondary mb-4 leading-relaxed">
                                {{ $cat->description }}
                            </p>
                        </div>
                        <a href="{{ route('opportunities.index', ['sector' => $cat->slug]) }}" class="text-xs font-semibold text-ink-primary hover:underline inline-flex items-center gap-1">
                            Explore Sector &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Why Managed Service (Editorial Narrative) -->
    <section class="py-16 md:py-20 bg-surface-paper border-b border-border-subtle">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-12">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted block mb-2">
                    Operational Model
                </span>
                <h2 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary mb-4">
                    The Difference Between a Directory and Managed Coordination
                </h2>
                <p class="text-base text-ink-secondary leading-relaxed">
                    Most opportunity portals sell contact data to multiple vendors indiscriminately. Franchise Dekho acts as an independent coordination partner, protecting your privacy and evaluating opportunity viability before you commit capital.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Pillar 1 -->
                <div class="space-y-3">
                    <div class="w-8 h-8 rounded-full bg-ink-primary text-white flex items-center justify-center font-serif font-bold text-sm">
                        1
                    </div>
                    <h3 class="font-serif text-lg font-bold text-ink-primary">
                        Independent Evidence Audits
                    </h3>
                    <p class="text-sm text-ink-secondary leading-relaxed">
                        Every brand listing undergoes legal identity verification, MCA entity matching, trademark review, and territory availability audits before receiving a publication badge.
                    </p>
                </div>

                <!-- Pillar 2 -->
                <div class="space-y-3">
                    <div class="w-8 h-8 rounded-full bg-ink-primary text-white flex items-center justify-center font-serif font-bold text-sm">
                        2
                    </div>
                    <h3 class="font-serif text-lg font-bold text-ink-primary">
                        Transparent Financial Disclosures
                    </h3>
                    <p class="text-sm text-ink-secondary leading-relaxed">
                        We separate brand marketing projections from disclosed reality. You see explicit line items for franchise fees, fit-out costs, equipment requirements, and royalty schedules.
                    </p>
                </div>

                <!-- Pillar 3 -->
                <div class="space-y-3">
                    <div class="w-8 h-8 rounded-full bg-ink-primary text-white flex items-center justify-center font-serif font-bold text-sm">
                        3
                    </div>
                    <h3 class="font-serif text-lg font-bold text-ink-primary">
                        Consented Human Coordination
                    </h3>
                    <p class="text-sm text-ink-secondary leading-relaxed">
                        Your documentation and financial capacity are never blasted across the internet. A dedicated case manager coordinates applications only after you explicitly review and authorize the submission.
                    </p>
                </div>
            </div>

            <!-- Call to Action Band -->
            <div class="mt-14 p-8 bg-surface-canvas rounded-sm border border-border-subtle flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h4 class="font-serif text-lg font-bold text-ink-primary mb-1">
                        Need tailored guidance for your city and budget?
                    </h4>
                    <p class="text-sm text-ink-muted">
                        Submit a confidential needs assessment. Our research team will shortlist viable opportunities.
                    </p>
                </div>
                <x-ui.button href="{{ route('enquiry.create') }}" variant="primary" class="whitespace-nowrap">
                    Submit Needs Assessment
                </x-ui.button>
            </div>
        </div>
    </section>
</x-layout.app>
