<x-layout.app 
    title="Explore Verified Business Opportunities — Franchise Dekho"
    metaDescription="Discover audited Indian franchise, dealership, and distributor opportunities. Independent corporate entity verification, itemized CapEx, and territory status."
    :canonical="route('opportunities.index')"
>
    <!-- Breadcrumb Navigation -->
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Opportunities']
    ]">
        <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
        <span class="text-xs text-ink-muted">Server-Rendered Index</span>
    </x-ui.breadcrumbs>
    <!-- Header Band on Warm Paper -->
    <section class="bg-surface-paper border-b border-border-subtle py-10 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted block mb-2">
                    Verified Opportunity Catalog
                </span>
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink-primary mb-3">
                    Discover Audited Business Opportunities Across India
                </h1>
                <p class="text-sm md:text-base text-ink-secondary leading-relaxed">
                    Every published opportunity below has undergone Ministry of Corporate Affairs entity validation, trademark/franchise contract checks, and verified financial disclosures.
                </p>
            </div>
        </div>
    </section>

    <!-- Main Catalog Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Filter Form Bar -->
        <form action="{{ route('opportunities.index') }}" method="GET" class="bg-surface-canvas p-4 sm:p-5 border border-border-subtle rounded-sm shadow-sm mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Search Query -->
                <div>
                    <label for="q" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                        Keyword / Brand
                    </label>
                    <input type="text" id="q" name="q" value="{{ $selectedFilters['q'] }}" placeholder="e.g. Chai Point, Courier, QSR..." class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                </div>

                <!-- Sector Filter -->
                <div>
                    <label for="sector" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                        Sector
                    </label>
                    <select id="sector" name="sector" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                        <option value="">All Sectors</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ $selectedFilters['sector'] === $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
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
                        <option value="under-5l" {{ $selectedFilters['budget'] === 'under-5l' ? 'selected' : '' }}>Under ₹5 Lakh</option>
                        <option value="5l-15l" {{ $selectedFilters['budget'] === '5l-15l' ? 'selected' : '' }}>₹5 Lakh - ₹15 Lakh</option>
                        <option value="15l-30l" {{ $selectedFilters['budget'] === '15l-30l' ? 'selected' : '' }}>₹15 Lakh - ₹30 Lakh</option>
                        <option value="30l-plus" {{ $selectedFilters['budget'] === '30l-plus' ? 'selected' : '' }}>₹30 Lakh+</option>
                    </select>
                </div>

                <!-- State Filter -->
                <div>
                    <label for="state" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                        State
                    </label>
                    <select id="state" name="state" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                        <option value="">All India</option>
                        <option value="UP" {{ $selectedFilters['state'] === 'UP' ? 'selected' : '' }}>Uttar Pradesh</option>
                        <option value="MH" {{ $selectedFilters['state'] === 'MH' ? 'selected' : '' }}>Maharashtra</option>
                        <option value="DL" {{ $selectedFilters['state'] === 'DL' ? 'selected' : '' }}>Delhi NCR</option>
                        <option value="KA" {{ $selectedFilters['state'] === 'KA' ? 'selected' : '' }}>Karnataka</option>
                        <option value="RJ" {{ $selectedFilters['state'] === 'RJ' ? 'selected' : '' }}>Rajasthan</option>
                        <option value="BR" {{ $selectedFilters['state'] === 'BR' ? 'selected' : '' }}>Bihar</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-3 border-t border-border-subtle">
                <!-- Sort Dropdown -->
                <div class="flex items-center gap-2 text-xs">
                    <label for="sort" class="text-ink-muted whitespace-nowrap">Sort by:</label>
                    <select id="sort" name="sort" class="text-xs bg-surface-canvas border border-border-subtle rounded-sm px-2 py-1 text-ink-secondary focus:border-ink-primary">
                        <option value="recently_verified" {{ $selectedFilters['sort'] === 'recently_verified' ? 'selected' : '' }}>Recently Verified</option>
                        <option value="investment_asc" {{ $selectedFilters['sort'] === 'investment_asc' ? 'selected' : '' }}>Investment: Low to High</option>
                        <option value="investment_desc" {{ $selectedFilters['sort'] === 'investment_desc' ? 'selected' : '' }}>Investment: High to Low</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    @if(array_filter($selectedFilters, fn($v) => !empty($v) && $v !== 'recently_verified'))
                        <a href="{{ route('opportunities.index') }}" class="text-xs text-ink-muted hover:text-status-rejected-text transition-colors">
                            Reset Filters
                        </a>
                    @endif
                    <x-ui.button type="submit" variant="primary" class="!py-1.5 !px-4 !text-xs">
                        Apply Filters
                    </x-ui.button>
                </div>
            </div>
        </form>

        <!-- Results Counter Bar -->
        <div class="flex items-center justify-between mb-6 pb-2 border-b border-border-subtle">
            <p class="text-sm font-medium text-ink-primary">
                Showing <span class="font-bold">{{ $opportunities->total() }}</span> Verified Opportunities
            </p>
            <div class="text-xs text-ink-muted">
                Updated Daily &bull; Strict Non-Broker Audit
            </div>
        </div>

        <!-- Opportunities Grid -->
        @if($opportunities->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                @foreach($opportunities as $opportunity)
                    <x-opportunity.card :opportunity="$opportunity" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $opportunities->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center bg-surface-paper/40 border border-border-subtle rounded-sm">
                <div class="max-w-md mx-auto">
                    <span class="w-12 h-12 mx-auto rounded-full bg-surface-subtle flex items-center justify-center text-ink-muted mb-4 font-mono text-lg">
                        0
                    </span>
                    <h3 class="font-serif text-lg font-bold text-ink-primary mb-2">
                        No Verified Opportunities Found
                    </h3>
                    <p class="text-sm text-ink-secondary mb-6 leading-relaxed">
                        We did not find active verified opportunities matching your exact criteria. We do not display unverified or placeholder listings to pad search results.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('opportunities.index') }}" class="text-xs font-semibold text-ink-primary hover:underline">
                            Clear All Filters
                        </a>
                        <span class="text-ink-muted hidden sm:inline">&bull;</span>
                        <x-ui.button href="{{ route('enquiry.create') }}" variant="primary" class="!py-2 !px-4 !text-xs">
                            Submit Custom Needs Assessment
                        </x-ui.button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layout.app>
