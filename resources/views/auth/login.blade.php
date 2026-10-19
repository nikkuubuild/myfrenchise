<x-layout.app title="Sign In — Franchise Dekho">
    <div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                Secure Account Access
            </span>
            <h1 class="font-serif text-3xl font-bold text-ink-primary">
                Sign In to Franchise Dekho
            </h1>
            <p class="mt-2 text-xs text-ink-muted leading-relaxed">
                Track your active franchise enquiries, verification shortlists, and coordinated brand applications.
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
                        <x-ui.alert variant="danger" title="Authentication Notice">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Email Address
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary @error('email') border-red-500 @enderror">
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted">
                                Password
                            </label>
                            <a href="{{ route('password.request') }}" class="text-xs text-ink-muted hover:text-ink-primary hover:underline">
                                Forgot Password?
                            </a>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary @error('password') border-red-500 @enderror">
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded-sm border-border-strong text-ink-primary focus:ring-ink-primary h-4 w-4">
                            <span class="text-xs text-ink-secondary">Keep me signed in on this device</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center !py-2.5">
                            Sign In to Portal &rarr;
                        </x-ui.button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-border-subtle text-center text-xs text-ink-muted">
                    <span>Don't have an account yet?</span>
                    <a href="{{ route('register') }}" class="font-semibold text-ink-primary hover:underline ml-1">
                        Register as an Entrepreneur
                    </a>
                </div>
            </div>

            <!-- Regulatory Notice -->
            <div class="mt-6 text-center text-[11px] text-ink-muted">
                <p>Protected by rate limiting &amp; session security &bull; No third-party data sharing</p>
            </div>
        </div>
    </div>
</x-layout.app>
