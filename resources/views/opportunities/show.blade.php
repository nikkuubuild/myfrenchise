<x-layout.app :title="$opportunity->title . ' — Verified Opportunity Profile | Franchise Dekho'">
    <!-- Top Breadcrumb & Status Bar -->
    <div class="bg-surface-paper border-b border-border-subtle py-3 text-xs text-ink-muted">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <nav class="flex items-center gap-2" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:underline">Home</a>
                <span>&rsaquo;</span>
                <a href="{{ route('opportunities.index') }}" class="hover:underline">Opportunities</a>
                <span>&rsaquo;</span>
                <a href="{{ route('opportunities.index', ['sector' => $opportunity->category->slug]) }}" class="hover:underline">
                    {{ $opportunity->category->name }}
                </a>
                <span>&rsaquo;</span>
                <span class="text-ink-primary font-medium truncate">{{ $opportunity->title }}</span>
            </nav>
            <div class="hidden sm:flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
                <span>Audit Freshness: Within 180 Days</span>
            </div>
        </div>
    </div>

    <!-- Opportunity Header Panel on Warm Paper -->
    <section class="bg-surface-paper border-b border-border-subtle py-10 md:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                            {{ $opportunity->category->name }}
                        </span>
                        @if($opportunity->isVerified())
                            <x-ui.badge variant="verified">
                                Verified Audit: {{ $opportunity->last_verified_at?->format('d M Y') }}
                            </x-ui.badge>
                        @endif
                        <span class="text-xs font-mono text-ink-muted px-2 py-0.5 bg-surface-canvas rounded-xs border border-border-subtle">
                            Model: {{ $opportunity->business_model }}
                        </span>
                    </div>

                    <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary mb-3">
                        {{ $opportunity->title }}
                    </h1>

                    <p class="text-sm text-ink-secondary mb-4">
                        Originating Entity: <strong class="text-ink-primary">{{ $opportunity->company->legal_name }}</strong>
                        @if($opportunity->company->cin)
                            &bull; <span class="font-mono text-xs">CIN: {{ $opportunity->company->cin }}</span>
                        @endif
                        @if($opportunity->company->incorporation_year)
                            &bull; <span>Est. {{ $opportunity->company->incorporation_year }}</span>
                        @endif
                    </p>

                    <p class="text-base text-ink-secondary leading-relaxed">
                        {{ $opportunity->summary }}
                    </p>
                </div>

                <!-- Sticky Header Action Card -->
                <div class="bg-surface-canvas p-6 rounded-sm border border-border-subtle shadow-sm md:w-80 flex-shrink-0">
                    <span class="text-xs text-ink-muted uppercase tracking-wider font-semibold block mb-1">
                        Disclosed Total Outlay
                    </span>
                    <div class="font-serif text-2xl font-bold text-ink-primary mb-1">
                        {{ $opportunity->formattedInvestmentRange() }}
                    </div>
                    <p class="text-xs text-ink-muted mb-6">
                        Excludes tenant working capital &bull; Verified items
                    </p>
                    <x-ui.button href="{{ route('enquiry.create', ['opportunity' => $opportunity->slug]) }}" variant="primary" class="w-full text-center mb-3">
                        Request Managed Coordination
                    </x-ui.button>
                    <span class="text-[11px] text-ink-muted text-center block">
                        Zero spam &bull; Direct brand case submission only with your consent
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Profile Disclosures -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Left 2 Cols: Comprehensive Disclosures -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Section 1: Detailed Overview -->
                <section>
                    <h2 class="font-serif text-2xl font-bold text-ink-primary mb-4 pb-2 border-b border-border-subtle">
                        Operational & Business Model
                    </h2>
                    <div class="prose text-sm text-ink-secondary leading-relaxed space-y-4">
                        <p>{{ $opportunity->description }}</p>
                    </div>
                </section>

                <!-- Section 2: Financial Outlay Table -->
                <section>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-border-subtle">
                        <div>
                            <h2 class="font-serif text-2xl font-bold text-ink-primary">
                                Itemized Financial Breakdown
                            </h2>
                            <p class="text-xs text-ink-muted mt-1">
                                Disclosed components with source verification evidence.
                            </p>
                        </div>
                    </div>

                    <x-opportunity.disclosure-table :opportunity="$opportunity" />
                </section>

                <!-- Section 3: Eligibility & Requirements -->
                <section>
                    <h2 class="font-serif text-2xl font-bold text-ink-primary mb-4 pb-2 border-b border-border-subtle">
                        Eligibility & Infrastructure Mandates
                    </h2>

                    <div class="space-y-3">
                        @forelse($opportunity->requirements as $req)
                            <div class="p-4 bg-surface-canvas border border-border-subtle rounded-sm flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-ink-primary mb-1">
                                        {{ $req->title }}
                                    </h3>
                                    @if($req->description)
                                        <p class="text-xs text-ink-secondary leading-relaxed">
                                            {{ $req->description }}
                                        </p>
                                    @endif
                                </div>
                                <span class="text-xs font-mono px-2 py-0.5 rounded-xs flex-shrink-0 {{ $req->is_mandatory ? 'bg-[#FDF4ED] text-status-caveat-text font-medium' : 'bg-surface-subtle text-ink-muted' }}">
                                    {{ $req->is_mandatory ? 'Mandatory' : 'Preferred' }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-ink-muted italic">Standard retail eligibility criteria applies.</p>
                        @endforelse
                    </div>
                </section>

                <!-- Section 4: Territory Availability -->
                <section>
                    <h2 class="font-serif text-2xl font-bold text-ink-primary mb-4 pb-2 border-b border-border-subtle">
                        Territory & Location Availability
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse($opportunity->locations as $loc)
                            <div class="p-3.5 bg-surface-canvas border border-border-subtle rounded-sm flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-sm text-ink-primary">
                                        {{ $loc->state_name }}
                                        @if($loc->district)
                                            <span class="text-ink-muted font-normal">({{ $loc->district }})</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-ink-muted mt-0.5">
                                        {{ $loc->territory_tier }} &bull; {{ $loc->is_exclusive ? 'Exclusivity Radius Protected' : 'Open Cluster' }}
                                    </div>
                                </div>
                                <span class="text-xs font-medium px-2 py-0.5 bg-accent-green-bg text-status-verified-text rounded-xs">
                                    {{ ucfirst($loc->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-ink-muted italic">Inquire with case manager for specific city availability.</p>
                        @endforelse
                    </div>
                </section>

                <!-- Section 5: Mandatory Legal Caveat -->
                <section>
                    <x-ui.alert variant="warning" title="Platform Advisory & Regulatory Caveats">
                        {{ $opportunity->disclaimer ?? 'Approval authority remains solely with the brand / franchisor entity. Franchise Dekho verifies commercial evidence and documentation schedules, but does not guarantee approvals, turnover, or business profitability.' }}
                    </x-ui.alert>
                </section>
            </div>

            <!-- Right 1 Col: Summary Sidebar -->
            <div class="space-y-6">
                <!-- Company Trust Card -->
                <div class="bg-surface-paper p-6 border border-border-subtle rounded-sm space-y-4">
                    <h3 class="font-serif text-lg font-bold text-ink-primary">
                        Audited Brand Profile
                    </h3>
                    <div class="text-xs space-y-2 text-ink-secondary">
                        <div class="flex justify-between py-1 border-b border-border-subtle">
                            <span class="text-ink-muted">Brand Name</span>
                            <span class="font-medium text-ink-primary">{{ $opportunity->company->brand_name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border-subtle">
                            <span class="text-ink-muted">Legal Entity</span>
                            <span class="font-medium text-ink-primary text-right truncate max-w-[160px]" title="{{ $opportunity->company->legal_name }}">{{ $opportunity->company->legal_name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border-subtle">
                            <span class="text-ink-muted">MCA Corporate ID</span>
                            <span class="font-mono text-ink-primary">{{ $opportunity->company->cin ?? 'Verified on record' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-border-subtle">
                            <span class="text-ink-muted">Registered State</span>
                            <span class="text-ink-primary">{{ $opportunity->company->operational_state ?? 'India' }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-ink-muted">Last Audit Date</span>
                            <span class="font-medium text-status-verified-text">{{ $opportunity->last_verified_at?->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Managed Service Guarantee Notice -->
                <div class="bg-surface-canvas p-6 border border-border-subtle rounded-sm">
                    <h4 class="font-serif text-base font-bold text-ink-primary mb-2">
                        How Franchise Dekho Assists You
                    </h4>
                    <ul class="text-xs text-ink-secondary space-y-2 leading-relaxed">
                        <li class="flex items-start gap-2">
                            <span class="text-status-verified-text font-bold">&check;</span>
                            <span>Dedicated human case manager assigned to your application.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-status-verified-text font-bold">&check;</span>
                            <span>Direct coordination with brand expansion heads.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-status-verified-text font-bold">&check;</span>
                            <span>Consented document sharing with full privacy audit trail.</span>
                        </li>
                    </ul>
                    <div class="mt-6">
                        <x-ui.button href="{{ route('enquiry.create', ['opportunity' => $opportunity->slug]) }}" variant="primary" class="w-full text-center">
                            Start Managed Application
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
