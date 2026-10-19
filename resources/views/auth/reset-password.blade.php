<x-layout.app title="Update Your Password — Franchise Dekho">
    <div class="min-h-[60vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                Account Security
            </span>
            <h1 class="font-serif text-3xl font-bold text-ink-primary">
                Set New Password
            </h1>
            <p class="mt-2 text-xs text-ink-muted leading-relaxed">
                Choose a strong new password for your account.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-surface-canvas py-8 px-4 sm:px-10 border border-border-subtle rounded-sm shadow-sm">
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

                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Email Address
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                               class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            New Password
                        </label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               placeholder="Min 8 chars, mixed case & numbers"
                               class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Confirm New Password
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               placeholder="Re-enter new password"
                               class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <div>
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center !py-2.5">
                            Update Password &rarr;
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
