<div id="fd-cookie-banner" class="hidden fixed bottom-4 right-4 left-4 md:left-auto md:max-w-md z-50 bg-surface-canvas border border-border-strong rounded-sm p-5 shadow-lg text-ink-primary transition-all duration-300" role="region" aria-label="Cookie and Privacy Consent">
    <div class="flex items-start gap-3">
        <div class="p-2 bg-surface-paper border border-border-subtle rounded-xs text-ink-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <div class="space-y-2 flex-grow">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-primary">Privacy &amp; Cookie Consent</h3>
                <span class="text-[10px] font-mono uppercase bg-accent-green-bg text-status-verified-text px-1.5 py-0.5 rounded-xs border border-status-verified-border">
                    DPDPA 2023
                </span>
            </div>
            <p class="text-xs text-ink-secondary leading-relaxed">
                We use strictly essential cookies for secure sessions and fraud prevention. We never sell your personal information or deploy unsolicited third-party advertising trackers.
            </p>
            <div class="pt-2 flex flex-wrap items-center gap-2">
                <button type="button" id="fd-accept-all-cookies" class="px-3 py-1.5 bg-ink-primary text-white text-xs font-medium rounded-sm hover:bg-accent-navy-light cursor-pointer shadow-sm">
                    Accept All
                </button>
                <button type="button" id="fd-essential-only-cookies" class="px-3 py-1.5 bg-surface-paper border border-border-strong text-ink-primary text-xs font-medium rounded-sm hover:bg-surface-canvas cursor-pointer">
                    Essential Only
                </button>
                <a href="{{ route('pages.cookie-policy') }}" class="text-xs text-ink-muted hover:text-ink-primary underline ml-1">
                    Cookie Policy
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const consentKey = 'fd_cookie_consent';
        const banner = document.getElementById('fd-cookie-banner');
        const acceptAllBtn = document.getElementById('fd-accept-all-cookies');
        const essentialBtn = document.getElementById('fd-essential-only-cookies');

        function getSavedConsent() {
            try {
                const stored = localStorage.getItem(consentKey);
                if (stored) return JSON.parse(stored);
            } catch (e) {}
            return null;
        }

        function saveConsent(preferences) {
            const consentData = {
                essential: true,
                analytics: preferences.analytics || false,
                timestamp: new Date().toISOString()
            };
            try {
                localStorage.setItem(consentKey, JSON.stringify(consentData));
                document.cookie = consentKey + '=' + encodeURIComponent(JSON.stringify(consentData)) + '; path=/; max-age=31536000; SameSite=Lax';
            } catch (e) {}
            if (banner) {
                banner.classList.add('hidden');
            }
        }

        if (!getSavedConsent() && banner) {
            banner.classList.remove('hidden');
        }

        if (acceptAllBtn) {
            acceptAllBtn.addEventListener('click', function () {
                saveConsent({ analytics: true });
            });
        }

        if (essentialBtn) {
            essentialBtn.addEventListener('click', function () {
                saveConsent({ analytics: false });
            });
        }
    });
</script>
