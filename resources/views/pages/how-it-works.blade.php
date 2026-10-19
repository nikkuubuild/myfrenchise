<x-layout.app title="How Managed Opportunity Coordination Works — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted block mb-2">
                    Platform Architecture
                </span>
                <h1 class="font-serif text-3xl md:text-5xl font-bold text-ink-primary mb-4">
                    How Managed Coordination Protects Entrepreneurs
                </h1>
                <p class="text-base md:text-lg text-ink-secondary leading-relaxed">
                    Most Indian business directories act as lead-generation engines that sell your phone number to dozens of brokers. Franchise Dekho functions as an independent coordination layer with strict verification standards.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        <!-- Stage 1 -->
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="w-12 h-12 rounded-full bg-ink-primary text-white flex items-center justify-center font-serif text-xl font-bold flex-shrink-0">
                1
            </div>
            <div class="space-y-3">
                <h2 class="font-serif text-2xl font-bold text-ink-primary">
                    Independent Originator Audits
                </h2>
                <p class="text-sm text-ink-secondary leading-relaxed">
                    Before any opportunity is listed, our compliance researchers cross-reference the franchisor entity against the Ministry of Corporate Affairs (MCA) database, verify active trademark ownership with the Controller General of Patents, and confirm representative authority. Unverified claims remain unpublished.
                </p>
            </div>
        </div>

        <!-- Stage 2 -->
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="w-12 h-12 rounded-full bg-ink-primary text-white flex items-center justify-center font-serif text-xl font-bold flex-shrink-0">
                2
            </div>
            <div class="space-y-3">
                <h2 class="font-serif text-2xl font-bold text-ink-primary">
                    Itemized Financial Transparency
                </h2>
                <p class="text-sm text-ink-secondary leading-relaxed">
                    We require companies to disclose itemized setup costs rather than generic promotional marketing ranges. You see explicit line items for franchise licensing fees, machinery, architectural fit-out, and recurring royalty schedules with documented evidence dates.
                </p>
            </div>
        </div>

        <!-- Stage 3 -->
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="w-12 h-12 rounded-full bg-ink-primary text-white flex items-center justify-center font-serif text-xl font-bold flex-shrink-0">
                3
            </div>
            <div class="space-y-3">
                <h2 class="font-serif text-2xl font-bold text-ink-primary">
                    Consented Human Case Coordination
                </h2>
                <p class="text-sm text-ink-secondary leading-relaxed">
                    When you request coordination, your documents and financial capacity are never blasted across the internet. A dedicated human case manager reviews your territory requirements, coordinates communication, and submits applications only to brands you explicitly authorize.
                </p>
            </div>
        </div>

        <!-- Stage 4 -->
        <div class="flex flex-col md:flex-row gap-8 items-start">
            <div class="w-12 h-12 rounded-full bg-ink-primary text-white flex items-center justify-center font-serif text-xl font-bold flex-shrink-0">
                4
            </div>
            <div class="space-y-3">
                <h2 class="font-serif text-2xl font-bold text-ink-primary">
                    Brand Approval & Commercial Closing
                </h2>
                <p class="text-sm text-ink-secondary leading-relaxed">
                    The franchisor, brand, or financial institution retains sole authority to review and approve your application. We assist with checklist preparation, site review coordination, and transparent contract evaluation to prevent surprise clauses before you sign.
                </p>
            </div>
        </div>

        <!-- CTA Box -->
        <div class="p-8 bg-surface-paper border border-border-subtle rounded-sm flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="font-serif text-xl font-bold text-ink-primary mb-1">
                    Ready to explore audited opportunities?
                </h3>
                <p class="text-xs text-ink-muted">
                    Filter by verified budget and state availability.
                </p>
            </div>
            <x-ui.button href="{{ route('opportunities.index') }}" variant="primary">
                Browse Verified Catalog
            </x-ui.button>
        </div>
    </div>
</x-layout.app>
