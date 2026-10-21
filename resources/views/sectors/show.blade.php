<x-layout.app 
    :title="$category->name . ' Franchise & Dealership Opportunities in India — Franchise Dekho'"
    :metaDescription="'Explore verified ' . $category->name . ' franchise opportunities in India. Independent entity validation, equipment schedules, and coordinated application management.'"
    :canonical="route('sectors.show', $category->slug)"
    :schema="$schema"
>
    <!-- Breadcrumb Navigation -->
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Sectors', 'url' => route('opportunities.index')],
        ['label' => $category->name]
    ]">
        <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
        <span class="text-xs text-ink-muted">Regulatory Research Grounded</span>
    </x-ui.breadcrumbs>

    <!-- Sector Hero Header -->
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                        Sector Focus & Due Diligence Guide
                    </span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-ink-primary mb-4">
                    {{ $category->name }} Franchises in India
                </h1>
                <p class="text-base sm:text-lg text-ink-secondary leading-relaxed mb-6">
                    {{ $category->description }}
                </p>

                <!-- Benchmark Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-border-subtle">
                    <div>
                        <span class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">Commercial Space</span>
                        <span class="text-sm font-semibold text-ink-primary">{{ $regulatoryGuidance['typical_space'] }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">Payback Horizon</span>
                        <span class="text-sm font-semibold text-ink-primary">{{ $regulatoryGuidance['typical_breakeven'] }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">Active Opportunities</span>
                        <span class="text-sm font-semibold text-status-verified-text">{{ $opportunities->total() }} Verified</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Regulatory Compliance & Due Diligence Box -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                <div class="max-w-2xl">
                    <h2 class="font-serif text-lg font-bold text-ink-primary mb-2">
                        Statutory Licensing & Due Diligence in {{ $category->name }}
                    </h2>
                    <p class="text-xs text-ink-secondary leading-relaxed mb-4">
                        {{ $regulatoryGuidance['key_check'] }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($regulatoryGuidance['acts'] as $act)
                            <span class="inline-flex items-center text-xs font-mono bg-surface-paper border border-border-subtle px-2.5 py-1 rounded-xs text-ink-primary">
                                &sect; {{ $act }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="md:w-72 flex flex-col justify-center border-t md:border-t-0 md:border-l border-border-subtle pt-4 md:pt-0 md:pl-6">
                    <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">Assisted Application</span>
                    <p class="text-xs text-ink-secondary mb-3">Our team coordinates directly with brand legal and expansion heads.</p>
                    <x-ui.button href="{{ route('enquiry.create') }}" variant="secondary" class="text-xs text-center">
                        Request Sector Assessment
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    <!-- Opportunities Grid in this Sector -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-serif text-2xl font-bold text-ink-primary">
                Verified Opportunities in {{ $category->name }}
            </h2>
            <a href="{{ route('opportunities.index', ['sector' => $category->slug]) }}" class="text-xs font-semibold text-accent-navy hover:underline">
                View in Filterable Catalog &rarr;
            </a>
        </div>

        @if($opportunities->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($opportunities as $opportunity)
                    <x-opportunity.card :opportunity="$opportunity" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $opportunities->links() }}
            </div>
        @else
            <div class="bg-surface-paper border border-border-subtle rounded-sm p-10 text-center">
                <p class="text-sm text-ink-muted mb-4">
                    There are currently no published opportunities in this sector meeting full verification audit standards.
                </p>
                <x-ui.button href="{{ route('opportunities.index') }}" variant="secondary">
                    Browse All Sectors
                </x-ui.button>
            </div>
        @endif
    </section>
</x-layout.app>
