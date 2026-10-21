<x-layout.app title="503 Service Unavailable — Maintenance Mode — Franchise Dekho" robots="noindex, nofollow">
    <div class="min-h-[60vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-accent-amber-bg border border-border-strong text-status-pending-text mx-auto font-mono text-2xl font-bold">
                503
            </div>

            <div class="space-y-2">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-status-pending-text block">
                    System Maintenance Underway
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary">
                    Platform Upgrade in Progress
                </h1>
                <p class="text-sm sm:text-base text-ink-secondary leading-relaxed max-w-md mx-auto">
                    Franchise Dekho is momentarily undergoing scheduled system maintenance and database updates to maintain enterprise security and operational compliance.
                </p>
            </div>

            <div class="bg-surface-paper border border-border-subtle rounded-sm p-4 text-xs text-ink-muted text-left space-y-1 max-w-md mx-auto font-mono">
                <div>&bull; Operational Notice: Routine Platform Maintenance &amp; Schema Migration</div>
                <div>&bull; Target Availability: Estimated restoration within 5–10 minutes</div>
            </div>

            <div class="pt-4 flex items-center justify-center gap-3">
                <x-ui.button href="javascript:window.location.reload();" variant="primary" class="text-xs">
                    Refresh Page
                </x-ui.button>
                <x-ui.button href="{{ url('/health') }}" variant="secondary" class="text-xs">
                    Check System Health
                </x-ui.button>
            </div>
        </div>
    </div>
</x-layout.app>
