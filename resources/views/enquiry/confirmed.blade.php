<x-layout.app title="Assessment Confirmed — Franchise Dekho">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
        <!-- Success Badge Indicator -->
        <div class="w-16 h-16 rounded-full bg-accent-green-bg border border-status-verified-border text-status-verified-text flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
            Assessment Received &bull; Consent Recorded
        </span>

        <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink-primary mb-4">
            Your Needs Assessment is Under Active Review
        </h1>

        <p class="text-sm md:text-base text-ink-secondary leading-relaxed mb-8">
            Thank you for submitting your business opportunity criteria. A dedicated Franchise Dekho case coordinator has been assigned to your file.
        </p>

        <!-- Tracking Reference Receipt Card -->
        <div class="bg-surface-paper p-6 border border-border-subtle rounded-sm max-w-md mx-auto mb-10 text-left">
            <span class="text-[11px] font-mono text-ink-muted uppercase tracking-wider block mb-1">
                Your Case Tracking Reference
            </span>
            <div class="font-mono text-xl font-bold text-ink-primary tracking-wide mb-3">
                {{ $referenceCode }}
            </div>
            <div class="text-xs text-ink-secondary space-y-2 pt-3 border-t border-border-subtle">
                <div class="flex items-start gap-2">
                    <span class="text-status-verified-text font-bold">&check;</span>
                    <span><strong>Next 24-48 Hours:</strong> Initial phone orientation and opportunity territory check.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-status-verified-text font-bold">&check;</span>
                    <span><strong>Consented Shortlist:</strong> We share documented options matching your budget.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-status-verified-text font-bold">&check;</span>
                    <span><strong>Zero Unsolicited Calls:</strong> Your contact is never released to third-party telemarketers.</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <x-ui.button href="{{ route('opportunities.index') }}" variant="secondary">
                Continue Exploring Catalog
            </x-ui.button>
            <x-ui.button href="{{ url('/') }}" variant="ghost">
                Return to Homepage
            </x-ui.button>
        </div>
    </div>
</x-layout.app>
