<x-layout.app title="429 Too Many Requests — Franchise Dekho" robots="noindex, nofollow">
    <div class="min-h-[60vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-status-disputed-bg border border-status-disputed-border text-status-disputed-text mx-auto font-mono text-2xl font-bold">
                429
            </div>

            <div class="space-y-2">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-status-disputed-text block">
                    Rate Limit Exceeded
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary">
                    Request Frequency Throttled
                </h1>
                <p class="text-sm sm:text-base text-ink-secondary leading-relaxed max-w-md mx-auto">
                    You have made too many requests in a short duration. Automated scraping, brute-force requests, and repetitive rapid submissions are temporarily throttled to protect platform stability.
                </p>
            </div>

            <div class="bg-surface-paper border border-border-subtle rounded-sm p-4 text-xs text-ink-muted text-center max-w-md mx-auto font-mono">
                Please wait 60 seconds before initiating additional requests.
            </div>

            <div class="pt-4 flex items-center justify-center gap-3">
                <x-ui.button href="{{ url('/') }}" variant="secondary" class="text-xs">
                    Return to Homepage
                </x-ui.button>
            </div>
        </div>
    </div>
</x-layout.app>
