<x-layout.app title="404 Not Found — Franchise Dekho" robots="noindex, nofollow">
    <div class="min-h-[60vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-surface-paper border border-border-strong text-ink-primary mx-auto font-mono text-2xl font-bold">
                404
            </div>

            <div class="space-y-2">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted block">
                    Page or Record Not Found
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary">
                    Requested Resource Unavailable
                </h1>
                <p class="text-sm sm:text-base text-ink-secondary leading-relaxed max-w-md mx-auto">
                    The requested page, opportunity profile, or document docket does not exist, has been moved, or was unpublished due to audit expiration.
                </p>
            </div>

            <div class="pt-4 flex items-center justify-center gap-3">
                <x-ui.button href="{{ url('/') }}" variant="secondary" class="text-xs">
                    Return to Homepage
                </x-ui.button>
                <x-ui.button href="{{ route('opportunities.index') }}" variant="primary" class="text-xs">
                    Explore Verified Opportunities
                </x-ui.button>
            </div>
        </div>
    </div>
</x-layout.app>
