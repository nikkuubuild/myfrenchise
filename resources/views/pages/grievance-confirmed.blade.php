<x-layout.app title="Grievance Received — Franchise Dekho">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="bg-surface-canvas border border-border-strong rounded-sm p-8 sm:p-12 text-center space-y-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-accent-green-bg border border-status-verified-border text-status-verified-text mb-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <div class="space-y-2">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block">
                    Statutory Acknowledgment Notice
                </span>
                <h1 class="font-serif text-2xl sm:text-4xl font-bold text-ink-primary">
                    Formal Grievance Formally Logged
                </h1>
                <p class="text-sm text-ink-secondary max-w-lg mx-auto leading-relaxed">
                    Your complaint has been registered in the compliance ledger. Under the Information Technology (Intermediary Guidelines) Rules, an acknowledgment has been recorded and an investigation docket created.
                </p>
            </div>

            <!-- Tracking Code Banner -->
            <div class="bg-surface-paper border border-border-strong rounded-sm p-6 max-w-md mx-auto space-y-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted block">
                    Complaint Tracking Number
                </span>
                <div class="font-mono text-2xl sm:text-3xl font-bold text-ink-primary tracking-wider select-all">
                    {{ $trackingCode }}
                </div>
                <p class="text-[11px] text-ink-muted">
                    Save this tracking code to look up investigation progress and formal resolution findings.
                </p>
            </div>

            <!-- SLAs Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-md mx-auto text-left text-xs bg-surface-canvas border border-border-subtle p-4 rounded-xs">
                <div>
                    <span class="text-ink-muted block uppercase text-[10px]">Statutory Acknowledgment:</span>
                    <strong class="text-ink-primary font-mono">Within 24 Hours</strong>
                </div>
                <div>
                    <span class="text-ink-muted block uppercase text-[10px]">Statutory Resolution:</span>
                    <strong class="text-ink-primary font-mono">Within 15 Days</strong>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('grievance.track', ['tracking_code' => $trackingCode]) }}" class="w-full sm:w-auto px-6 py-2.5 bg-ink-primary text-white text-xs font-semibold rounded-sm hover:bg-accent-navy-light transition-colors shadow-sm">
                    Track Complaint Status &rarr;
                </a>
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-2.5 bg-surface-paper border border-border-strong text-ink-primary text-xs font-semibold rounded-sm hover:bg-surface-canvas transition-colors">
                    Return to Homepage
                </a>
            </div>
        </div>
    </div>
</x-layout.app>
