<x-layout.app title="419 Page Expired — Franchise Dekho" robots="noindex, nofollow">
    <div class="min-h-[60vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-surface-paper border border-border-strong text-ink-primary mx-auto font-mono text-2xl font-bold">
                419
            </div>

            <div class="space-y-2">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted block">
                    Security Token Expired
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary">
                    Session Security Check
                </h1>
                <p class="text-sm sm:text-base text-ink-secondary leading-relaxed max-w-md mx-auto">
                    Your form submission token has expired due to session inactivity. This security measure prevents Cross-Site Request Forgery (CSRF) and protects your submitted data.
                </p>
            </div>

            <div class="pt-4 flex items-center justify-center gap-3">
                <x-ui.button href="javascript:window.location.reload();" variant="primary" class="text-xs">
                    Refresh and Retry Form
                </x-ui.button>
                <x-ui.button href="{{ url('/') }}" variant="secondary" class="text-xs">
                    Return to Homepage
                </x-ui.button>
            </div>
        </div>
    </div>
</x-layout.app>
