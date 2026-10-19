<x-layout.app title="Privacy Notice &amp; Data Protection (DPDPA 2023) — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                    Statutory Compliance &bull; DPDPA 2023
                </span>
                <h1 class="font-serif text-3xl md:text-5xl font-bold text-ink-primary mb-4">
                    Privacy Notice &amp; Data Protection Standard
                </h1>
                <p class="text-base text-ink-secondary leading-relaxed">
                    How Franchise Dekho collects, safeguards, and processes commercial applicant data in strict accordance with the Digital Personal Data Protection Act (DPDPA) 2023 and the Information Technology Rules.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12 text-sm text-ink-secondary leading-relaxed">
        
        <!-- Summary Callout -->
        <div class="p-6 bg-surface-canvas border-l-4 border-accent-green border-border-subtle rounded-sm space-y-2">
            <h3 class="font-serif text-base font-bold text-ink-primary">Our Core Data Pledge to Indian Entrepreneurs</h3>
            <p class="text-xs leading-relaxed text-ink-secondary">
                We never sell, broadcast, or auction your phone number or business records to unregulated telemarketers. Every disclosure of your documents or contact details requires your explicit, recorded consent for a specific verified franchisor.
            </p>
        </div>

        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">1. Role of Franchise Dekho as Data Fiduciary</h2>
            <p>
                Under the Digital Personal Data Protection Act, 2023 ("DPDPA"), Franchise Dekho operates as a <strong>Data Fiduciary</strong> in respect of the personal data provided by prospective franchisees, business investors, and brand owners. We process personal data solely for lawful purposes directly connected to business-opportunity discovery, eligibility assessment, and consented application coordination.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">2. Categories of Personal Data Processed</h2>
            <p>We adhere strictly to the principle of data minimization. The data we collect includes:</p>
            <ul class="list-disc list-inside space-y-1.5 pl-2">
                <li><strong>Identity &amp; Contact:</strong> Full name, verified mobile number, email address, city, and state of residence.</li>
                <li><strong>Commercial Eligibility Parameters:</strong> Available capital budget band, targeted geographic operating territories, available commercial carpet area, and business experience level.</li>
                <li><strong>Confidential Application Documents:</strong> Identity proofs, address proofs, commercial profile statements, and franchisor questionnaires uploaded voluntarily to the private case vault.</li>
                <li><strong>Consent Records:</strong> Cryptographic timestamps and purpose identifiers when you explicitly grant or revoke document-sharing consent.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">3. No Unsolicited Telemarketing or Lead Resale</h2>
            <p>
                Franchise Dekho strictly prohibits third-party lead trading. Your personal contact details and private financial capacity declarations are never broadcast to directories or shared with third parties without your affirmative, transaction-specific consent.
            </p>
        </section>

        <section class="space-y-4">
            <h2 class="font-serif text-xl font-bold text-ink-primary">4. Statutory Rights of the Data Principal (Section 11-14, DPDPA)</h2>
            <p>As a Data Principal under Indian law, you possess enforceable rights regarding your personal data:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm space-y-2">
                    <h3 class="font-semibold text-xs uppercase tracking-wider text-ink-primary">Right to Information &amp; Portability</h3>
                    <p class="text-xs text-ink-muted">You have the right to receive an export summary of all personal data being processed and the identity of franchisors with whom it was shared.</p>
                    @auth
                        <a href="{{ route('customer.data-export') }}" class="inline-flex items-center text-xs font-medium text-accent-green hover:underline pt-1">
                            Download Data Export (JSON) &rarr;
                        </a>
                    @else
                        <span class="text-[11px] text-ink-muted block pt-1">Log in to your account to download machine-readable export.</span>
                    @endauth
                </div>

                <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm space-y-2">
                    <h3 class="font-semibold text-xs uppercase tracking-wider text-ink-primary">Right to Correction &amp; Erasure</h3>
                    <p class="text-xs text-ink-muted">You have the right to request rectification of inaccurate data or complete erasure of your account and personal identifiers.</p>
                    @auth
                        <a href="{{ route('customer.data-deletion.show') }}" class="inline-flex items-center text-xs font-medium text-red-700 hover:underline pt-1">
                            Manage Account Erasure &rarr;
                        </a>
                    @else
                        <span class="text-[11px] text-ink-muted block pt-1">Log in to initiate permanent account anonymization and erasure.</span>
                    @endauth
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h2 class="font-serif text-xl font-bold text-ink-primary">5. Data Retention &amp; Automated Scrubbing Policy</h2>
            <p>
                Franchise Dekho retains personal records only for the duration necessary to satisfy the commercial coordination purpose for which it was consented.
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2">
                <li><strong>Registered Accounts:</strong> Retained while your account remains active. Anonymized upon verified erasure request.</li>
                <li><strong>Unlinked Guest Inquiries:</strong> Unregistered public inquiries that do not result in an active account are automatically scrubbed and anonymized after 180 days.</li>
                <li><strong>Private Case Vault Files:</strong> Customer identity documents are stored on encrypted private local disks outside public web directories.</li>
            </ul>
        </section>

        <section class="space-y-4 bg-surface-canvas p-6 border border-border-subtle rounded-sm">
            <h2 class="font-serif text-xl font-bold text-ink-primary">6. Statutory Grievance Redressal (IT Rules 2021)</h2>
            <p class="text-xs text-ink-secondary leading-relaxed">
                If you believe your personal data has been handled inconsistently with our policies or Indian data protection regulations, you have the right to file a formal grievance with our designated officer.
            </p>
            <div class="p-4 bg-surface-paper border border-border-subtle rounded-xs text-xs font-mono space-y-1 text-ink-primary">
                <div><strong>Designated Grievance Officer:</strong> Chief Compliance &amp; Grievance Officer</div>
                <div><strong>Address:</strong> Franchise Dekho Platform Operations, Cyber City, Gurugram, Haryana 122002</div>
                <div><strong>Email:</strong> grievance@franchisedekho.in / privacy@franchisedekho.in</div>
                <div><strong>Acknowledgment SLA:</strong> Within 24 hours of receipt</div>
                <div><strong>Statutory Resolution SLA:</strong> Within 15 working days</div>
            </div>
            <div class="pt-1">
                <a href="{{ route('pages.grievance') }}" class="inline-flex items-center text-xs font-semibold text-ink-primary underline hover:text-accent-navy-light">
                    File a Formal Grievance or Privacy Complaint Online &rarr;
                </a>
            </div>
        </section>
    </div>
</x-layout.app>
