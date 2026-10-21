<x-layout.app 
    title="AI-Assisted Business Opportunity Discovery — Franchise Dekho"
    metaDescription="Ask questions and discover verified Indian franchise opportunities with answers grounded strictly in MCA audited corporate records, verified CapEx, and statutory compliance."
    :canonical="route('assistant.index')"
>
    <!-- Breadcrumb Navigation -->
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'AI Discovery Assistant']
    ]">
        <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
        <span class="text-xs text-ink-muted">Grounded in Audited Records</span>
    </x-ui.breadcrumbs>

    <!-- Header Hero on Warm Paper -->
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 mb-3">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                    Strictly Grounded Retrieval Engine
                </span>
                <span class="text-xs font-mono text-ink-muted px-2 py-0.5 bg-surface-canvas rounded-xs border border-border-subtle">
                    T15 Core
                </span>
            </div>
            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-ink-primary mb-4 leading-tight">
                AI-Assisted Franchise Discovery
            </h1>
            <p class="text-base sm:text-lg text-ink-secondary leading-relaxed max-w-3xl">
                Explore verified business opportunities, evaluate commercial CapEx outlays, and understand statutory licensing obligations under Indian law—with answers grounded strictly in verified evidentiary records.
            </p>

            <!-- Grounding Pillars -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-8 mt-8 border-t border-border-subtle text-xs text-ink-muted">
                <div class="flex items-start gap-2.5">
                    <span class="text-status-verified-text font-bold text-sm">&check;</span>
                    <div>
                        <strong class="text-ink-primary block mb-0.5">Approved Records Only</strong>
                        <span>Zero hallucinations; strictly references published audits.</span>
                    </div>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="text-status-verified-text font-bold text-sm">&check;</span>
                    <div>
                        <strong class="text-ink-primary block mb-0.5">Transparent Uncertainty</strong>
                        <span>Explicitly reports missing data rather than guessing.</span>
                    </div>
                </div>
                <div class="flex items-start gap-2.5">
                    <span class="text-status-verified-text font-bold text-sm">&check;</span>
                    <div>
                        <strong class="text-ink-primary block mb-0.5">Zero Fabricated Claims</strong>
                        <span>No fake earnings guarantees, ratings, or auto-approvals.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Query Interface -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Search Card -->
        <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 sm:p-8 shadow-xs mb-8">
            <form method="POST" action="{{ route('assistant.query') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="ai-query" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-2 font-semibold">
                        Ask a discovery or due diligence question:
                    </label>
                    <div class="relative">
                        <textarea 
                            id="ai-query" 
                            name="query" 
                            rows="3" 
                            required 
                            minlength="3" 
                            maxlength="500"
                            placeholder="e.g. What verified food & beverage kiosks are available under ₹10 Lakh in Uttar Pradesh with space under 250 sq ft?"
                            class="w-full text-sm sm:text-base bg-surface-paper border border-border-strong rounded-sm px-4 py-3 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary transition-colors leading-relaxed"
                        >{{ old('query', $initialQuery) }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                    <div class="text-xs text-ink-muted font-mono">
                        Protected by server-side rate limits &bull; Max 500 chars
                    </div>
                    <x-ui.button type="submit" variant="primary" class="w-full sm:w-auto text-sm justify-center">
                        Synthesize Grounded Answer &rarr;
                    </x-ui.button>
                </div>
            </form>

            <!-- Suggested Prompt Chips -->
            <div class="mt-6 pt-6 border-t border-border-subtle">
                <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted block mb-2.5">
                    Suggested Exploration Prompts:
                </span>
                <div class="flex flex-wrap gap-2">
                    @foreach($suggestedPrompts as $prompt)
                        <a href="{{ route('assistant.index', ['q' => $prompt]) }}" 
                           class="inline-block text-xs font-mono bg-surface-paper hover:bg-surface-canvas border border-border-subtle px-3 py-1.5 rounded-xs text-ink-secondary hover:text-ink-primary hover:border-ink-muted transition-colors">
                            &rsaquo; {{ $prompt }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Synthesized Output Dossier -->
        @if(!empty($result))
            <div class="space-y-8 animate-fade-in">
                <!-- Guardrail Intervention Banner -->
                @if($result['status'] === 'guardrail_intervention')
                    <div class="bg-surface-paper border-l-4 border-status-disputed-border rounded-xs p-6 shadow-xs">
                        <div class="flex items-start gap-3">
                            <span class="text-status-disputed-text font-bold text-lg leading-none">&excl;</span>
                            <div class="space-y-2">
                                <h3 class="font-serif text-base font-bold text-ink-primary">
                                    Governance &amp; Safety Policy Guardrail Triggered
                                </h3>
                                <p class="text-sm text-ink-secondary leading-relaxed">
                                    {{ $result['answer'] }}
                                </p>
                                @if(!empty($result['statutory_sources']))
                                    <div class="pt-2 text-xs font-mono text-ink-muted">
                                        Governing Statutes: {{ implode(' &bull; ', $result['statutory_sources']) }}
                                    </div>
                                @endif
                                <div class="pt-3 flex flex-wrap gap-3">
                                    <x-ui.button href="{{ url('/how-it-works') }}" variant="secondary" class="text-xs">
                                        View Platform Operating Model
                                    </x-ui.button>
                                    <x-ui.button href="{{ url('/enquiry') }}" variant="primary" class="text-xs">
                                        Request Assisted Human Search
                                    </x-ui.button>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Grounded Narrative Card -->
                    <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 sm:p-8 shadow-xs space-y-6">
                        <!-- Verification Status Header -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-border-subtle text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-status-verified-text"></span>
                                <span class="font-mono font-semibold uppercase tracking-wider text-status-verified-text">
                                    Grounded in Audited Records
                                </span>
                            </div>
                            <span class="font-mono text-ink-muted">
                                Generated: {{ date('d M Y, H:i T') }}
                            </span>
                        </div>

                        <!-- Narrative Answer -->
                        <div class="prose max-w-none text-ink-primary text-sm sm:text-base leading-relaxed space-y-4">
                            {!! nl2br(e($result['answer'])) !!}
                        </div>

                        <!-- Matched Opportunities Showcase -->
                        @if($result['matched_opportunities']->isNotEmpty())
                            <div class="pt-6 border-t border-border-subtle">
                                <h3 class="font-serif text-lg font-bold text-ink-primary mb-4">
                                    Verified Opportunities Cited in Answer
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($result['matched_opportunities'] as $opp)
                                        <div class="bg-surface-paper border border-border-subtle rounded-sm p-5 flex flex-col justify-between hover:border-ink-muted transition-colors">
                                            <div>
                                                <div class="flex items-center justify-between gap-2 mb-2">
                                                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                                                        {{ $opp->category?->name }}
                                                    </span>
                                                    <x-ui.badge variant="verified">
                                                        Audit: {{ $opp->last_verified_at?->format('d M Y') }}
                                                    </x-ui.badge>
                                                </div>
                                                <h4 class="font-serif text-base font-bold text-ink-primary mb-1">
                                                    <a href="{{ route('opportunities.show', $opp->slug) }}" class="hover:underline">
                                                        {{ $opp->title }}
                                                    </a>
                                                </h4>
                                                <p class="text-xs text-ink-secondary mb-3">
                                                    Entity: {{ $opp->company->legal_name }}
                                                </p>
                                                <div class="text-xs text-ink-primary font-medium py-1 px-2 bg-surface-canvas rounded-xs border border-border-subtle mb-3">
                                                    Outlay: {{ \App\Models\Opportunity::formatInr($opp->min_investment) }} - {{ \App\Models\Opportunity::formatInr($opp->max_investment) }}
                                                </div>
                                            </div>
                                            <div class="pt-3 border-t border-border-subtle flex items-center justify-between text-xs">
                                                <a href="{{ route('opportunities.show', $opp->slug) }}" class="font-semibold text-accent-navy hover:underline">
                                                    View Disclosures &rarr;
                                                </a>
                                                <a href="{{ route('enquiry.create', ['opportunity' => $opp->slug]) }}" class="px-2.5 py-1 bg-ink-primary text-white rounded-xs hover:bg-accent-navy-light transition-colors">
                                                    Apply
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Statutory Citations Box -->
                        @if(!empty($result['statutory_sources']))
                            <div class="bg-surface-paper border border-border-subtle rounded-xs p-4 text-xs space-y-1.5">
                                <strong class="text-ink-primary block font-mono uppercase tracking-wider">
                                    Statutory Frameworks &amp; Evidentiary Citations:
                                </strong>
                                <ul class="list-disc list-inside text-ink-secondary space-y-1 font-mono">
                                    @foreach($result['statutory_sources'] as $src)
                                        <li>{{ $src }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Transparent Uncertainty Disclosures -->
                        @if(!empty($result['uncertainty_notes']))
                            <div class="bg-surface-canvas border-l-2 border-border-strong p-4 text-xs text-ink-muted space-y-1">
                                <strong class="text-ink-primary block">
                                    Transparent Limitations &amp; Required Physical Verifications:
                                </strong>
                                <ul class="list-disc list-inside space-y-0.5">
                                    @foreach($result['uncertainty_notes'] as $note)
                                        <li>{{ $note }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Mandatory Regulatory & AI Disclaimer Notice -->
                <div class="bg-surface-paper border border-border-subtle rounded-sm p-5 text-xs text-ink-muted leading-relaxed">
                    <p class="font-semibold text-ink-primary mb-1">
                        Mandatory Non-Negotiable AI Governance &amp; Advisory Notice:
                    </p>
                    {{ $result['disclaimer'] }}
                </div>
            </div>
        @endif
    </div>
</x-layout.app>
