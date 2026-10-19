@props(['opportunity'])

<article class="bg-surface-canvas border border-border-subtle rounded-sm p-6 hover:border-ink-primary transition-colors flex flex-col justify-between">
    <div>
        <!-- Top Metadata & Verification Badge -->
        <div class="flex items-start justify-between gap-4 mb-3">
            <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                {{ $opportunity->category->name }}
            </span>
            @if($opportunity->isVerified())
                <x-ui.badge variant="verified" size="sm">
                    Verified: {{ $opportunity->last_verified_at?->format('d M Y') }}
                </x-ui.badge>
            @endif
        </div>

        <!-- Title & Company -->
        <h3 class="font-serif text-xl font-bold text-ink-primary mb-1">
            <a href="{{ route('opportunities.show', $opportunity->slug) }}" class="hover:underline">
                {{ $opportunity->title }}
            </a>
        </h3>
        <p class="text-xs text-ink-muted mb-4">
            by <span class="font-medium text-ink-secondary">{{ $opportunity->company->brand_name }}</span>
            @if($opportunity->company->cin)
                &bull; <span class="font-mono text-[11px]">CIN Verified</span>
            @endif
        </p>

        <!-- Summary -->
        <p class="text-sm text-ink-secondary mb-6 line-clamp-2 leading-relaxed">
            {{ $opportunity->summary }}
        </p>

        <!-- Key Disclosed Metrics Grid -->
        <div class="grid grid-cols-2 gap-3 py-3 border-y border-border-subtle text-xs mb-6">
            <div>
                <span class="block text-ink-muted mb-0.5">Estimated Outlay</span>
                <span class="font-semibold text-ink-primary text-sm">
                    {{ $opportunity->formattedInvestmentRange() }}
                </span>
            </div>
            <div>
                <span class="block text-ink-muted mb-0.5">Space Required</span>
                <span class="font-medium text-ink-secondary">
                    @if($opportunity->min_space_sqft)
                        {{ $opportunity->min_space_sqft }} - {{ $opportunity->max_space_sqft ?? $opportunity->min_space_sqft }} sq ft
                    @else
                        Flexible / Kiosk
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-ink-muted mb-0.5">Operating Model</span>
                <span class="font-medium text-ink-secondary">
                    {{ $opportunity->business_model }}
                </span>
            </div>
            <div>
                <span class="block text-ink-muted mb-0.5">Territory Status</span>
                <span class="font-medium text-status-verified-text">
                    {{ $opportunity->locations->count() }} Available Regions
                </span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between gap-3 pt-2">
        <a href="{{ route('opportunities.show', $opportunity->slug) }}" class="text-xs font-semibold text-ink-primary hover:underline">
            View Disclosures &rarr;
        </a>
        <x-ui.button href="{{ route('enquiry.create', ['opportunity' => $opportunity->slug]) }}" variant="secondary" class="!py-1.5 !px-3 !text-xs">
            Request Coordination
        </x-ui.button>
    </div>
</article>
