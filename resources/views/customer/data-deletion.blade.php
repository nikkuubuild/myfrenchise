<x-layout.app title="Data Erasure &amp; Account Deletion — Franchise Dekho" robots="noindex, nofollow">
    <section class="bg-surface-paper border-b border-border-subtle py-10 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('dashboard') }}" class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted hover:text-ink-primary">
                        &larr; Return to Dashboard
                    </a>
                    <span class="text-xs text-ink-muted">&bull;</span>
                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-red-700">
                        DPDPA 2023 Statutory Right
                    </span>
                </div>
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink-primary">
                    Right to Erasure &amp; Account Deletion
                </h1>
                <p class="text-sm text-ink-secondary mt-2 leading-relaxed">
                    Under the Digital Personal Data Protection Act, 2023, you have the statutory right to withdraw consent and request the permanent erasure and anonymization of your personal data.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <!-- Critical Warning Box -->
        <div class="bg-red-50/60 border-l-4 border-red-700 border border-red-200 rounded-sm p-6 space-y-3">
            <h2 class="font-serif text-base font-bold text-red-950">
                Notice of Irreversible Action
            </h2>
            <p class="text-xs text-red-900 leading-relaxed">
                Proceeding with this request will permanently erase your personal identity records from Franchise Dekho. This action cannot be undone.
            </p>
            <ul class="text-xs text-red-800 list-disc list-inside space-y-1.5 pl-1">
                <li>Your name, phone number, email address, and location will be permanently wiped or anonymized.</li>
                <li>All active document-sharing consents granted to brand franchisors will be immediately revoked.</li>
                <li>Your customer dashboard access will be immediately terminated.</li>
                @if ($enquiriesCount > 0)
                    <li>You have <strong>{{ $enquiriesCount }}</strong> commercial enquiry/case record(s) on file. Their personal coordinates will be completely redacted.</li>
                @endif
            </ul>
        </div>

        <!-- Before Deleting Suggestion (Export Data) -->
        <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-xs uppercase tracking-wider text-ink-primary">Want to Keep a Copy First?</h3>
                <p class="text-xs text-ink-muted mt-0.5">
                    Download a complete machine-readable archive of your personal data and case records before proceeding.
                </p>
            </div>
            <div>
                <a href="{{ route('customer.data-export') }}" class="inline-flex items-center px-4 py-2 bg-surface-paper border border-border-strong rounded-xs text-xs font-semibold text-ink-primary hover:bg-surface-canvas shadow-xs whitespace-nowrap">
                    Download Personal Archive (JSON) &rarr;
                </a>
            </div>
        </div>

        <!-- Confirmation Form -->
        <div class="bg-surface-canvas border border-border-strong rounded-sm p-6 sm:p-8 space-y-6">
            <div class="border-b border-border-subtle pb-4">
                <h3 class="font-serif text-lg font-bold text-ink-primary">
                    Confirm Identity &amp; Authorize Erasure
                </h3>
                <p class="text-xs text-ink-muted mt-1">
                    To prevent accidental or malicious deletion, please authenticate with your current account password.
                </p>
            </div>

            @if ($errors->any())
                <x-ui.alert variant="danger">
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('customer.data-deletion.destroy') }}" class="space-y-6 text-xs">
                @csrf

                <div>
                    <label for="current_password" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                        Current Account Password <span class="text-red-600">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password" required placeholder="Enter your password to authorize" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-red-600 focus:border-red-600">
                </div>

                <div class="p-4 bg-surface-paper border border-border-subtle rounded-xs">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="confirmation" value="1" required class="mt-0.5 rounded-xs border-border-strong text-red-600 focus:ring-red-600">
                        <span class="text-xs text-ink-secondary leading-relaxed">
                            I solemnly confirm that I wish to permanently erase my Franchise Dekho customer account and anonymize all associated personal records under the DPDPA 2023. I understand this action is immediate and irrevocable.
                        </span>
                    </label>
                </div>

                <div class="pt-2 border-t border-border-subtle flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('dashboard') }}" class="text-xs text-ink-muted hover:text-ink-primary underline order-2 sm:order-1">
                        Cancel &bull; Keep My Account
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-red-700 text-white text-xs font-semibold rounded-sm hover:bg-red-800 transition-colors shadow-sm cursor-pointer order-1 sm:order-2">
                        Permanently Erase Account &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout.app>
