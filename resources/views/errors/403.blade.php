<x-layout.app title="403 Forbidden — Access Restricted — Franchise Dekho" robots="noindex, nofollow">
    <div class="min-h-[60vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl w-full text-center space-y-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-status-disputed-bg border border-status-disputed-border text-status-disputed-text mx-auto font-mono text-2xl font-bold">
                403
            </div>

            <div class="space-y-2">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-status-disputed-text block">
                    Access Restricted by Security Policy
                </span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary">
                    Authorization Required
                </h1>
                <p class="text-sm sm:text-base text-ink-secondary leading-relaxed max-w-md mx-auto">
                    {{ $exception->getMessage() ?: 'You do not have permission to access this case workspace, document dossier, or administrative endpoint under platform privacy rules.' }}
                </p>
            </div>

            <div class="bg-surface-paper border border-border-subtle rounded-sm p-4 text-xs text-ink-muted text-left space-y-1 max-w-md mx-auto font-mono">
                <div>&bull; Boundary: Strict Case Isolation &amp; Role-Gated Access</div>
                <div>&bull; Statutory Standard: DPDPA 2023 Consented Document Storage</div>
            </div>

            <div class="pt-4 flex items-center justify-center gap-3">
                <x-ui.button href="{{ url('/') }}" variant="secondary" class="text-xs">
                    Return to Homepage
                </x-ui.button>
                @auth
                    <x-ui.button href="{{ route('dashboard') }}" variant="primary" class="text-xs">
                        Go to My Dashboard
                    </x-ui.button>
                @else
                    <x-ui.button href="{{ route('login') }}" variant="primary" class="text-xs">
                        Sign In with Credentials
                    </x-ui.button>
                @endauth
            </div>
        </div>
    </div>
</x-layout.app>
