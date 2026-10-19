<x-layout.app title="Reset Your Password — Franchise Dekho">
    <div class="min-h-[60vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                Account Recovery
            </span>
            <h1 class="font-serif text-3xl font-bold text-ink-primary">
                Reset Password
            </h1>
            <p class="mt-2 text-xs text-ink-muted leading-relaxed">
                Enter your registered email address and we'll send you a secure link to reset your account password.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-surface-canvas py-8 px-4 sm:px-10 border border-border-subtle rounded-sm shadow-sm">
                @if (session('status'))
                    <div class="mb-6">
                        <x-ui.alert variant="success">
                            {{ session('status') }}
                        </x-ui.alert>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6">
                        <x-ui.alert variant="danger">
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Registered Email Address
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <div>
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center !py-2.5">
                            Send Reset Link &rarr;
                        </x-ui.button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-border-subtle text-center text-xs text-ink-muted">
                    <a href="{{ route('login') }}" class="font-semibold text-ink-primary hover:underline">
                        &larr; Return to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
