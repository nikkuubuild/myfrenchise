<x-layout.app title="Grievance Redressal &amp; Fraud Reporting — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-3xl">
                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-status-rejected-text block mb-2">
                        Statutory Mechanism &bull; IT Rules 2021 &amp; Consumer Protection
                    </span>
                    <h1 class="font-serif text-3xl md:text-5xl font-bold text-ink-primary mb-4">
                        Grievance Officer &amp; Fraud Reporting Route
                    </h1>
                    <p class="text-base text-ink-secondary leading-relaxed">
                        Franchise Dekho maintains a zero-tolerance policy against unauthorized brand impersonation, fraudulent fee demands, and privacy violations. Every complaint is tracked under a statutory SLA.
                    </p>
                </div>
                <div>
                    <a href="{{ route('grievance.track') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-surface-canvas border border-border-strong rounded-sm text-xs font-semibold text-ink-primary hover:border-ink-primary shadow-xs">
                        <svg class="w-4 h-4 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Track Existing Complaint
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Left 2 Cols: Complaint Intake Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 sm:p-8">
                    <div class="border-b border-border-subtle pb-4 mb-6">
                        <h2 class="font-serif text-xl font-bold text-ink-primary">
                            Submit a Formal Complaint or Fraud Alert
                        </h2>
                        <p class="text-xs text-ink-muted mt-1">
                            Reports are assigned an immutable tracking code (#FD-GRV-YYYYMM-XXXX) and routed directly to our designated Grievance Officer.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6">
                            <x-ui.alert variant="danger">
                                <span class="font-semibold block mb-1">Please correct the following errors before submitting:</span>
                                <ul class="list-disc list-inside text-xs space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </x-ui.alert>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('grievance.store') }}" class="space-y-6 text-xs">
                        @csrf

                        @if ($targetOpportunity)
                            <input type="hidden" name="opportunity_id" value="{{ $targetOpportunity->id }}">
                            <div class="p-3 bg-surface-paper border border-border-subtle rounded-xs flex items-center justify-between">
                                <span class="text-ink-secondary">Pertaining to Opportunity: <strong>{{ $targetOpportunity->title }}</strong></span>
                                <span class="font-mono text-[10px] text-ink-muted">ID #{{ $targetOpportunity->id }}</span>
                            </div>
                        @endif

                        <!-- Reporter Category & Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="reporter_type" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Your Role / Capacity <span class="text-red-600">*</span>
                                </label>
                                <select id="reporter_type" name="reporter_type" required class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                                    <option value="">Select your role...</option>
                                    @foreach ($reporterTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('reporter_type') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="category" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Violation Category <span class="text-red-600">*</span>
                                </label>
                                <select id="category" name="category" required class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                                    <option value="">Select category...</option>
                                    @foreach ($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Contact Coordinates -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="full_name" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Full Name <span class="text-red-600">*</span>
                                </label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', auth()->user()?->name) }}" required placeholder="Your legal name" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                            </div>

                            <div>
                                <label for="email" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Email Address <span class="text-red-600">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required placeholder="email@domain.com" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                            </div>

                            <div>
                                <label for="phone" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                    Mobile Number <span class="text-red-600">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required placeholder="+91 98765 43210" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                            </div>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Complaint Subject / Brand Involved <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="Brief summary of issue (e.g. Unauthorized fee requested by broker claiming brand authorization)" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                        </div>

                        <!-- Narrative Description -->
                        <div>
                            <label for="description" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Detailed Statement of Facts <span class="text-red-600">*</span>
                            </label>
                            <textarea id="description" name="description" rows="5" required placeholder="Provide a chronological account: dates, names of individuals, communications, specific amounts demanded, or unconsented data disclosures..." class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary leading-relaxed">{{ old('description') }}</textarea>
                        </div>

                        <!-- Evidence Summary -->
                        <div>
                            <label for="evidence_summary" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Available Evidence Summary <span class="text-ink-muted font-normal">(Optional)</span>
                            </label>
                            <textarea id="evidence_summary" name="evidence_summary" rows="3" placeholder="List available evidence (e.g. WhatsApp chat exports, bank deposit slips, email headers, audio recordings). Our officer will request copies during acknowledgment." class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary leading-relaxed">{{ old('evidence_summary') }}</textarea>
                        </div>

                        <!-- Statutory Declaration -->
                        <div class="pt-2">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="declaration" value="1" required class="mt-0.5 rounded-xs border-border-strong text-ink-primary focus:ring-ink-primary">
                                <span class="text-xs text-ink-secondary leading-relaxed">
                                    I hereby declare and affirm under penalty of applicable law that the statements made above are true and complete to the best of my knowledge and belief. I understand that submitting knowingly false or malicious claims against brand partners constitutes an offense.
                                </span>
                            </label>
                        </div>

                        <!-- Action Submit -->
                        <div class="pt-2 border-t border-border-subtle flex items-center justify-between">
                            <span class="text-[11px] text-ink-muted">
                                SLA: Acknowledgment in 24 hours &bull; Formal resolution in 15 days
                            </span>
                            <button type="submit" class="px-6 py-2.5 bg-ink-primary text-white text-xs font-semibold rounded-sm hover:bg-accent-navy-light transition-colors shadow-sm cursor-pointer">
                                Submit Formal Grievance &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Col: Statutory Officer Contact & Disclosures -->
            <div class="space-y-6">
                <div class="bg-surface-paper border border-border-subtle rounded-sm p-6 space-y-4">
                    <span class="text-[11px] font-mono font-semibold uppercase tracking-wider text-accent-green block">
                        Statutory Compliance Officer
                    </span>
                    <h3 class="font-serif text-lg font-bold text-ink-primary">
                        Designated Grievance Officer
                    </h3>
                    <p class="text-xs text-ink-secondary leading-relaxed">
                        In accordance with the Information Technology (Intermediary Guidelines and Digital Media Ethics Code) Rules, 2021 and Consumer Protection (E-Commerce) Rules, 2020:
                    </p>

                    <div class="p-4 bg-surface-canvas border border-border-subtle rounded-xs text-xs font-mono space-y-2 text-ink-primary">
                        <div>
                            <span class="text-ink-muted block text-[10px] uppercase">Officer Name &amp; Title</span>
                            Chief Compliance &amp; Grievance Officer
                        </div>
                        <div>
                            <span class="text-ink-muted block text-[10px] uppercase">Physical Service Address</span>
                            Franchise Dekho Operations Center<br>
                            DLF Cyber City, Tower B, Level 8<br>
                            Gurugram, Haryana 122002, India
                        </div>
                        <div>
                            <span class="text-ink-muted block text-[10px] uppercase">Statutory Email</span>
                            <a href="mailto:grievance@franchisedekho.in" class="underline font-semibold">grievance@franchisedekho.in</a>
                        </div>
                    </div>

                    <div class="space-y-2 text-xs text-ink-muted border-t border-border-subtle pt-3">
                        <div class="flex items-center justify-between">
                            <span>Acknowledgment SLA:</span>
                            <strong class="text-ink-primary font-mono">24 Hours</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Statutory Resolution SLA:</span>
                            <strong class="text-ink-primary font-mono">15 Working Days</strong>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-3">
                    <h4 class="font-serif text-sm font-bold text-ink-primary">
                        Priority Escalation Triggers
                    </h4>
                    <p class="text-xs text-ink-secondary leading-relaxed">
                        The following reports receive immediate priority routing and expedited company outreach within 4 hours:
                    </p>
                    <ul class="text-xs text-ink-secondary space-y-2 list-disc list-inside">
                        <li>Demanding unreceipted cash payments or unauthorized processing fees.</li>
                        <li>Unauthorized entities falsely marketing a brand without trademark ownership.</li>
                        <li>Unconsented leakage or sale of applicant customer data.</li>
                        <li>Guaranteed turnover promises or fabricated bank sanction letters.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
