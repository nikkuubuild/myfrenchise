<x-layout.app title="Needs Assessment & Managed Service Enquiry — Franchise Dekho">
    <!-- Header Band on Warm Paper -->
    <section class="bg-surface-paper border-b border-border-subtle py-10 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted block mb-2">
                    Managed Application Coordination
                </span>
                <h1 class="font-serif text-3xl md:text-4xl font-bold text-ink-primary mb-3">
                    Confidential Needs Assessment
                </h1>
                <p class="text-sm md:text-base text-ink-secondary leading-relaxed">
                    Submit your investment budget and geographic preferences. Our research team reviews feasibility and assigns a dedicated case manager before presenting approved options.
                </p>
            </div>
        </div>
    </section>

    <!-- Main Form Container -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Target Opportunity Notice if Preselected -->
        @if($targetOpportunity)
            <div class="mb-8 p-4 bg-accent-green-bg border border-status-verified-border rounded-sm flex items-center justify-between gap-4">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-status-verified-text block font-semibold">
                        Pre-selected Target Opportunity
                    </span>
                    <strong class="text-ink-primary text-sm">{{ $targetOpportunity->title }}</strong>
                    <span class="text-xs text-ink-muted block">Estimated Outlay: {{ $targetOpportunity->formattedInvestmentRange() }}</span>
                </div>
                <a href="{{ route('enquiry.create') }}" class="text-xs text-ink-muted hover:text-ink-primary underline">
                    Change
                </a>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8">
                <x-ui.alert variant="danger" title="Please review the highlighted items:">
                    <ul class="list-disc list-inside text-xs mt-1 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            </div>
        @endif

        <form action="{{ route('enquiry.store') }}" method="POST" class="space-y-10">
            @csrf
            @if($targetOpportunity)
                <input type="hidden" name="opportunity_id" value="{{ $targetOpportunity->id }}">
            @endif

            <!-- Step 1: Capital Outlay & Readiness -->
            <div class="bg-surface-canvas p-6 sm:p-8 border border-border-subtle rounded-sm">
                <div class="flex items-center gap-3 mb-6 pb-3 border-b border-border-subtle">
                    <span class="w-6 h-6 rounded-full bg-ink-primary text-white flex items-center justify-center text-xs font-mono font-bold">
                        1
                    </span>
                    <h2 class="font-serif text-xl font-bold text-ink-primary">
                        Investment Capacity & Readiness
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="budget_range" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Total Investment Capacity <span class="text-status-rejected-text">*</span>
                        </label>
                        <select id="budget_range" name="budget_range" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                            <option value="">Select Budget Range</option>
                            <option value="under-5l" {{ old('budget_range') === 'under-5l' ? 'selected' : '' }}>Under ₹5 Lakh</option>
                            <option value="5l-15l" {{ old('budget_range') === '5l-15l' ? 'selected' : '' }}>₹5 Lakh - ₹15 Lakh</option>
                            <option value="15l-30l" {{ old('budget_range') === '15l-30l' ? 'selected' : '' }}>₹15 Lakh - ₹30 Lakh</option>
                            <option value="30l-plus" {{ old('budget_range') === '30l-plus' ? 'selected' : '' }}>₹30 Lakh+</option>
                        </select>
                        <span class="text-[11px] text-ink-muted block mt-1">Include franchise fee, setup CapEx, and initial inventory.</span>
                    </div>

                    <div>
                        <label for="preferred_timeline" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Target Launch Timeline <span class="text-status-rejected-text">*</span>
                        </label>
                        <select id="preferred_timeline" name="preferred_timeline" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                            <option value="immediate" {{ old('preferred_timeline') === 'immediate' ? 'selected' : '' }}>Immediate (Within 30 Days)</option>
                            <option value="1-3-months" {{ old('preferred_timeline', '1-3-months') === '1-3-months' ? 'selected' : '' }}>1 to 3 Months</option>
                            <option value="3-6-months" {{ old('preferred_timeline') === '3-6-months' ? 'selected' : '' }}>3 to 6 Months</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Location & Commercial Property -->
            <div class="bg-surface-canvas p-6 sm:p-8 border border-border-subtle rounded-sm">
                <div class="flex items-center gap-3 mb-6 pb-3 border-b border-border-subtle">
                    <span class="w-6 h-6 rounded-full bg-ink-primary text-white flex items-center justify-center text-xs font-mono font-bold">
                        2
                    </span>
                    <h2 class="font-serif text-xl font-bold text-ink-primary">
                        Target Location & Space Profile
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="target_state" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Operating State <span class="text-status-rejected-text">*</span>
                        </label>
                        <select id="target_state" name="target_state" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                            <option value="">Select State</option>
                            <option value="UP" {{ old('target_state') === 'UP' ? 'selected' : '' }}>Uttar Pradesh</option>
                            <option value="MH" {{ old('target_state') === 'MH' ? 'selected' : '' }}>Maharashtra</option>
                            <option value="DL" {{ old('target_state') === 'DL' ? 'selected' : '' }}>Delhi NCR</option>
                            <option value="KA" {{ old('target_state') === 'KA' ? 'selected' : '' }}>Karnataka</option>
                            <option value="RJ" {{ old('target_state') === 'RJ' ? 'selected' : '' }}>Rajasthan</option>
                            <option value="BR" {{ old('target_state') === 'BR' ? 'selected' : '' }}>Bihar</option>
                            <option value="MP" {{ old('target_state') === 'MP' ? 'selected' : '' }}>Madhya Pradesh</option>
                            <option value="WB" {{ old('target_state') === 'WB' ? 'selected' : '' }}>West Bengal</option>
                        </select>
                    </div>

                    <div>
                        <label for="target_city" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            City / District <span class="text-status-rejected-text">*</span>
                        </label>
                        <input type="text" id="target_city" name="target_city" value="{{ old('target_city') }}" required placeholder="e.g. Lucknow, Noida, Pune" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <div>
                        <label for="commercial_space_status" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Commercial Property Status <span class="text-status-rejected-text">*</span>
                        </label>
                        <select id="commercial_space_status" name="commercial_space_status" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                            <option value="owned" {{ old('commercial_space_status') === 'owned' ? 'selected' : '' }}>Owned Commercial Shop / Land</option>
                            <option value="leased" {{ old('commercial_space_status') === 'leased' ? 'selected' : '' }}>Currently Leased Commercial Space</option>
                            <option value="searching" {{ old('commercial_space_status', 'searching') === 'searching' ? 'selected' : '' }}>Searching for Property / No Space Yet</option>
                        </select>
                    </div>

                    <div>
                        <label for="carpet_area_sqft" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Carpet Area Available (Sq. Ft.)
                        </label>
                        <input type="number" id="carpet_area_sqft" name="carpet_area_sqft" value="{{ old('carpet_area_sqft') }}" placeholder="e.g. 250" min="50" max="50000" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                </div>
            </div>

            <!-- Step 3: Identity & Consented Authorization -->
            <div class="bg-surface-canvas p-6 sm:p-8 border border-border-subtle rounded-sm">
                <div class="flex items-center gap-3 mb-6 pb-3 border-b border-border-subtle">
                    <span class="w-6 h-6 rounded-full bg-ink-primary text-white flex items-center justify-center text-xs font-mono font-bold">
                        3
                    </span>
                    <h2 class="font-serif text-xl font-bold text-ink-primary">
                        Applicant Identity & Privacy Authorization
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="full_name" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Full Legal Name <span class="text-status-rejected-text">*</span>
                        </label>
                        <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="e.g. Rajesh Kumar" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Mobile Number <span class="text-status-rejected-text">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="e.g. 9876543210" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                        <span class="text-[11px] text-ink-muted block mt-1">Used exclusively for case coordination updates.</span>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Email Address <span class="text-status-rejected-text">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="e.g. rajesh@example.com" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>

                    <div>
                        <label for="business_experience" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Prior Business Background <span class="text-status-rejected-text">*</span>
                        </label>
                        <select id="business_experience" name="business_experience" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2.5 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                            <option value="first_time" {{ old('business_experience') === 'first_time' ? 'selected' : '' }}>First-time Entrepreneur</option>
                            <option value="existing_retail" {{ old('business_experience') === 'existing_retail' ? 'selected' : '' }}>Existing Retail / F&B Operator</option>
                            <option value="corporate_professional" {{ old('business_experience') === 'corporate_professional' ? 'selected' : '' }}>Corporate Executive / Professional</option>
                            <option value="other" {{ old('business_experience') === 'other' ? 'selected' : '' }}>Other Business Owner</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-2">
                            Specific Questions or Context
                        </label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Tell us about specific brand preferences, partner expectations, or questions..." class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Mandatory Explicit Consent Checkbox -->
                <div class="p-4 bg-surface-paper border border-border-subtle rounded-sm">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="consent" value="1" required {{ old('consent') ? 'checked' : '' }} class="mt-1 h-4 w-4 rounded-xs border-border-strong text-ink-primary focus:ring-ink-primary">
                        <span class="text-xs text-ink-secondary leading-relaxed">
                            <strong class="text-ink-primary block mb-0.5">Mandatory Consented Coordination Authorization:</strong>
                            I authorize Franchise Dekho case managers to review my assessment profile and research suitable business opportunities. I understand that Franchise Dekho will never sell or publicly broadcast my data, and will only share my profile with opportunities and franchisors that I explicitly authorize in writing.
                        </span>
                    </label>
                </div>
            </div>

            <!-- Submit Button & Disclaimer -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-xs text-ink-muted">
                    No upfront registration charges &bull; Strictly confidential
                </span>
                <x-ui.button type="submit" variant="primary" class="w-full sm:w-auto !py-3 !px-8 text-base">
                    Submit Needs Assessment
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layout.app>
