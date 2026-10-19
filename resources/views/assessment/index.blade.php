<x-layout.app title="Assessment & Opportunity Matcher — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                    Algorithmic Matching Engine
                </span>
                <h1 class="font-serif text-3xl md:text-5xl font-bold text-ink-primary mb-4">
                    Find Your Opportunity Compatibility Match
                </h1>
                <p class="text-base md:text-lg text-ink-secondary leading-relaxed">
                    Our matching engine runs your investment budget and geographic profile against independently audited opportunities. We apply hard filters first to ensure you only see viable, active options.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <form action="{{ route('assessment.match') }}" method="POST" class="bg-surface-canvas p-6 sm:p-8 border border-border-subtle rounded-sm shadow-sm space-y-8">
            @csrf

            <!-- Section 1: Capital Capacity -->
            <div>
                <h2 class="font-serif text-xl font-bold text-ink-primary mb-2">
                    1. Financial Outlay
                </h2>
                <p class="text-xs text-ink-muted mb-4">
                    Select your total initial capital capacity including setup, licensing, and security deposits.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="p-4 border border-border-subtle rounded-sm cursor-pointer hover:border-ink-primary transition-colors flex items-center gap-3">
                        <input type="radio" name="budget_range" value="under-5l" required class="text-ink-primary focus:ring-ink-primary">
                        <div>
                            <span class="font-bold text-sm text-ink-primary block">Under ₹5 Lakh</span>
                            <span class="text-xs text-ink-muted">Kiosks, CSP, Delivery hubs</span>
                        </div>
                    </label>
                    <label class="p-4 border border-border-subtle rounded-sm cursor-pointer hover:border-ink-primary transition-colors flex items-center gap-3">
                        <input type="radio" name="budget_range" value="5l-15l" checked class="text-ink-primary focus:ring-ink-primary">
                        <div>
                            <span class="font-bold text-sm text-ink-primary block">₹5 Lakh - ₹15 Lakh</span>
                            <span class="text-xs text-ink-muted">QSR tea cafes, Retail counters</span>
                        </div>
                    </label>
                    <label class="p-4 border border-border-subtle rounded-sm cursor-pointer hover:border-ink-primary transition-colors flex items-center gap-3">
                        <input type="radio" name="budget_range" value="15l-30l" class="text-ink-primary focus:ring-ink-primary">
                        <div>
                            <span class="font-bold text-sm text-ink-primary block">₹15 Lakh - ₹30 Lakh</span>
                            <span class="text-xs text-ink-muted">Dine-in restaurants, Pre-schools</span>
                        </div>
                    </label>
                    <label class="p-4 border border-border-subtle rounded-sm cursor-pointer hover:border-ink-primary transition-colors flex items-center gap-3">
                        <input type="radio" name="budget_range" value="30l-plus" class="text-ink-primary focus:ring-ink-primary">
                        <div>
                            <span class="font-bold text-sm text-ink-primary block">₹30 Lakh+</span>
                            <span class="text-xs text-ink-muted">Distributorships, Diagnostic centers</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Section 2: Operating Location -->
            <div class="pt-6 border-t border-border-subtle">
                <h2 class="font-serif text-xl font-bold text-ink-primary mb-2">
                    2. Geographic Territory
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="state" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Target State <span class="text-status-rejected-text">*</span>
                        </label>
                        <select id="state" name="state" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary">
                            <option value="">Select State</option>
                            <option value="UP">Uttar Pradesh</option>
                            <option value="MH">Maharashtra</option>
                            <option value="DL">Delhi NCR</option>
                            <option value="KA">Karnataka</option>
                            <option value="RJ">Rajasthan</option>
                            <option value="BR">Bihar</option>
                            <option value="MP">Madhya Pradesh</option>
                        </select>
                    </div>

                    <div>
                        <label for="district" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            City / District (Optional)
                        </label>
                        <input type="text" id="district" name="district" placeholder="e.g. Lucknow, Noida, Pune" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary">
                    </div>
                </div>
            </div>

            <!-- Section 3: Commercial Space & Preferences -->
            <div class="pt-6 border-t border-border-subtle">
                <h2 class="font-serif text-xl font-bold text-ink-primary mb-2">
                    3. Commercial Space &amp; Experience
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="carpet_area_sqft" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Available Space (Sq. Ft.)
                        </label>
                        <input type="number" id="carpet_area_sqft" name="carpet_area_sqft" placeholder="e.g. 200" min="50" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary">
                    </div>

                    <div>
                        <label for="sector" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Preferred Sector (Optional)
                        </label>
                        <select id="sector" name="sector" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary">
                            <option value="">All Sectors Considered</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="experience" class="block text-xs font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                            Your Background
                        </label>
                        <select id="experience" name="experience" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary focus:border-ink-primary">
                            <option value="first_time">First-time Entrepreneur</option>
                            <option value="existing_retail">Existing Retail / Outlet Owner</option>
                            <option value="corporate_professional">Corporate / Working Professional</option>
                            <option value="other">Other Business Experience</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-6 border-t border-border-subtle flex items-center justify-between">
                <span class="text-xs text-ink-muted">
                    Instant calculation &bull; No phone number required to view matches
                </span>
                <x-ui.button type="submit" variant="primary" class="!py-2.5 !px-6">
                    Calculate Match Compatibility &rarr;
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layout.app>
