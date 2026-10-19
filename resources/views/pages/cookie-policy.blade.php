<x-layout.app title="Cookie Policy &amp; Consent Standard — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                    Transparency &bull; Minimal Footprint
                </span>
                <h1 class="font-serif text-3xl md:text-5xl font-bold text-ink-primary mb-4">
                    Cookie Policy
                </h1>
                <p class="text-base text-ink-secondary leading-relaxed">
                    How Franchise Dekho utilizes essential first-party cookies to secure user sessions and respect your commercial privacy.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-10 text-sm text-ink-secondary leading-relaxed">
        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">1. Our Core Principle: Zero Unsolicited Tracking</h2>
            <p>
                Franchise Dekho is engineered on a "privacy-by-design" framework. Unlike traditional advertising directories, we do not monetize customer attention through surveillance pixels, behavioral advertising beacons, or third-party tracking networks.
            </p>
            <p>
                We only deploy cookies that are strictly necessary to deliver our managed opportunity discovery, authentication, and document coordination services.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="font-serif text-xl font-bold text-ink-primary">2. Cookies Deployed by Franchise Dekho</h2>
            <p>Below is an exhaustive inventory of the first-party cookies deployed on this application:</p>

            <div class="border border-border-subtle rounded-sm overflow-hidden bg-surface-canvas">
                <table class="min-w-full divide-y divide-border-subtle text-xs">
                    <thead class="bg-surface-paper text-ink-primary font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Cookie Name</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-left">Purpose</th>
                            <th class="px-4 py-3 text-left">Retention</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle text-ink-secondary font-mono">
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink-primary">laravel_session / fd_session</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 bg-accent-green-bg text-status-verified-text rounded-xs">Strictly Essential</span></td>
                            <td class="px-4 py-3 font-sans">Maintains secure user authentication state, session continuity, and role permissions across page requests.</td>
                            <td class="px-4 py-3">120 Minutes</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink-primary">XSRF-TOKEN</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 bg-accent-green-bg text-status-verified-text rounded-xs">Strictly Essential</span></td>
                            <td class="px-4 py-3 font-sans">Guards against Cross-Site Request Forgery (CSRF) attacks on state-changing forms and document uploads.</td>
                            <td class="px-4 py-3">Session</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-semibold text-ink-primary">fd_cookie_consent</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 bg-surface-paper text-ink-primary border border-border-strong rounded-xs">Functional Preference</span></td>
                            <td class="px-4 py-3 font-sans">Stores your explicit acknowledgment and preferences regarding cookie categories.</td>
                            <td class="px-4 py-3">365 Days</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">3. Third-Party Trackers &amp; Web Beacons</h2>
            <p>
                Franchise Dekho does not load Google Analytics, Meta Pixel, remarketing scripts, or any third-party advertising cookies. All business opportunity matching and application coordination are conducted on our first-party server infrastructure.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">4. Managing Your Preferences</h2>
            <p>
                You can reset or change your preferences at any time by clearing your browser's local storage and cookies for this domain. You may also configure your web browser to reject cookies, though doing so may prevent you from logging into your customer portal or submitting secured document files.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">5. Contact Information</h2>
            <p>
                If you have questions about our cookie practices or statutory compliance under the Digital Personal Data Protection Act (DPDPA) 2023, please contact our Grievance &amp; Compliance Officer at <a href="mailto:privacy@franchisedekho.in" class="text-ink-primary underline font-medium">privacy@franchisedekho.in</a> or visit our <a href="{{ route('pages.grievance') }}" class="text-ink-primary underline font-medium">Grievance Redressal Center</a>.
            </p>
        </section>
    </div>
</x-layout.app>
