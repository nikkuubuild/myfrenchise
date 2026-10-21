<x-layout.app title="Create Your Entrepreneur Account — Franchise Dekho" robots="noindex, nofollow">
    <div class="py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-xl text-center mb-8">
            <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                Entrepreneur Registration
            </span>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary">
                Create Your Franchise Dekho Account
            </h1>
            <p class="mt-2 text-sm text-ink-secondary leading-relaxed max-w-md mx-auto">
                Join our managed discovery platform to save verified opportunities, track applications, and coordinate with dedicated case specialists.
            </p>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-xl">
            <div class="bg-surface-canvas py-8 px-6 sm:px-10 border border-border-subtle rounded-sm shadow-sm">
                @if ($errors->any())
                    <div class="mb-6">
                        <x-ui.alert variant="danger" title="Please review the following items:">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Full Legal Name <span class="text-status-caveat-text">*</span>
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               placeholder="e.g. Rahul Sharma"
                               class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <!-- Email & Mobile -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Email Address <span class="text-status-caveat-text">*</span>
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                                   placeholder="rahul@example.com"
                                   class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Mobile Number <span class="text-status-caveat-text">*</span>
                            </label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                                   placeholder="10-digit mobile number"
                                   class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                        </div>
                    </div>

                    <!-- Location Preferences -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="state" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Target State <span class="text-status-caveat-text">*</span>
                            </label>
                            <select id="state" name="state" required
                                    class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                                <option value="">Select State</option>
                                <option value="MH" @selected(old('state') === 'MH')>Maharashtra</option>
                                <option value="KA" @selected(old('state') === 'KA')>Karnataka</option>
                                <option value="DL" @selected(old('state') === 'DL')>Delhi NCR</option>
                                <option value="UP" @selected(old('state') === 'UP')>Uttar Pradesh</option>
                                <option value="TN" @selected(old('state') === 'TN')>Tamil Nadu</option>
                                <option value="GJ" @selected(old('state') === 'GJ')>Gujarat</option>
                                <option value="WB" @selected(old('state') === 'WB')>West Bengal</option>
                                <option value="TS" @selected(old('state') === 'TS')>Telangana</option>
                                <option value="RJ" @selected(old('state') === 'RJ')>Rajasthan</option>
                                <option value="MP" @selected(old('state') === 'MP')>Madhya Pradesh</option>
                                <option value="OTHER" @selected(old('state') === 'OTHER')>Other State / UT</option>
                            </select>
                        </div>

                        <div>
                            <label for="city" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Operating City / District <span class="text-status-caveat-text">*</span>
                            </label>
                            <input id="city" type="text" name="city" value="{{ old('city') }}" required
                                   placeholder="e.g. Pune or Bengaluru"
                                   class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                        </div>
                    </div>

                    <!-- Password and Confirmation -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Password <span class="text-status-caveat-text">*</span>
                            </label>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                   placeholder="Min 8 chars, mixed case & numbers"
                                   class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                Confirm Password <span class="text-status-caveat-text">*</span>
                            </label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                   placeholder="Re-enter password"
                                   class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:outline-none focus:ring-1 focus:ring-ink-primary">
                        </div>
                    </div>

                    <!-- Mandatory Terms and Privacy Consent -->
                    <div class="pt-4 border-t border-border-subtle">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="terms_agreed" value="1" @checked(old('terms_agreed')) required
                                   class="mt-1 rounded-sm border-border-strong text-ink-primary focus:ring-ink-primary h-4 w-4">
                            <span class="text-xs text-ink-secondary leading-relaxed">
                                I confirm that all provided details are accurate. I have read and agree to the
                                <a href="{{ route('pages.terms') }}" target="_blank" class="text-ink-primary underline font-medium">Terms of Service</a>
                                and
                                <a href="{{ route('pages.privacy') }}" target="_blank" class="text-ink-primary underline font-medium">Privacy Notice</a>,
                                and understand that Franchise Dekho is an independent coordination platform that does not guarantee franchise approvals or profits.
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <x-ui.button type="submit" variant="primary" class="w-full justify-center !py-2.5">
                            Create Account &amp; Access Dashboard &rarr;
                        </x-ui.button>
                    </div>
                </form>

                <div class="mt-6 pt-6 border-t border-border-subtle text-center text-xs text-ink-muted">
                    <span>Already registered?</span>
                    <a href="{{ route('login') }}" class="font-semibold text-ink-primary hover:underline ml-1">
                        Sign In to Your Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
