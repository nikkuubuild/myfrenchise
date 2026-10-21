<x-layout.app 
    :title="$article->meta_title ?? ($article->title . ' — Due Diligence Guide | Franchise Dekho')"
    :metaDescription="$article->meta_description ?? $article->excerpt"
    :canonical="$article->canonical_url ?? route('guides.show', $article->slug)"
    ogType="article"
    :schema="$schema"
>
    <!-- Breadcrumbs -->
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Due Diligence Guides', 'url' => route('guides.index')],
        ['label' => $article->title]
    ]">
        @if($article->last_verified_at)
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
                <span class="text-xs text-ink-muted">Last Verified: {{ $article->last_verified_at->format('d M Y') }}</span>
            </div>
        @endif
    </x-ui.breadcrumbs>

    <!-- Article Header -->
    <header class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-4">
                @if($article->category)
                    <a href="{{ route('sectors.show', $article->category->slug) }}" class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green hover:underline">
                        {{ $article->category->name }}
                    </a>
                @else
                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted">
                        Legal Due Diligence
                    </span>
                @endif
                <span class="text-ink-muted">&bull;</span>
                <span class="text-xs font-mono text-ink-muted">
                    {{ $article->reading_time_minutes }} min read
                </span>
                @if($article->last_verified_at)
                    <span class="text-ink-muted">&bull;</span>
                    <span class="text-xs font-mono text-status-verified-text bg-accent-green-bg px-2 py-0.5 rounded-xs border border-status-verified-border">
                        Audit Verified
                    </span>
                @endif
            </div>

            <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-ink-primary tracking-tight leading-[1.2] mb-6">
                {{ $article->title }}
            </h1>

            <p class="text-lg md:text-xl text-ink-secondary leading-relaxed mb-8">
                {{ $article->excerpt }}
            </p>

            <!-- Author & Metadata Block -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-6 border-t border-border-subtle text-xs text-ink-muted">
                <div>
                    <span class="font-semibold text-ink-primary block">{{ $article->author_name }}</span>
                    <span>{{ $article->author_title }}</span>
                </div>
                <div class="sm:text-right">
                    <span>Published: {{ $article->published_at?->format('d F Y') ?? $article->created_at->format('d F Y') }}</span>
                    @if($article->last_verified_at)
                        <span class="block text-status-verified-text font-medium">Re-verified: {{ $article->last_verified_at->format('d F Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Article Body -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 sm:p-10 shadow-xs mb-10">
            <!-- Article Content Prose -->
            <div class="prose max-w-none text-ink-primary leading-relaxed text-sm sm:text-base space-y-6">
                {!! nl2br(e($article->content)) !!}
            </div>

            <!-- Statutory Sources & Citation Evidence -->
            @if($article->sources_and_evidence)
                <div class="mt-12 pt-8 border-t border-border-subtle">
                    <h3 class="font-serif text-base font-bold text-ink-primary mb-3">
                        Statutory References & Regulatory Authorities Cited
                    </h3>
                    <div class="bg-surface-paper border border-border-subtle rounded-xs p-4 text-xs text-ink-secondary leading-relaxed font-mono">
                        {!! nl2br(e($article->sources_and_evidence)) !!}
                    </div>
                </div>
            @endif

            <!-- Non-Negotiable Editorial Disclaimer -->
            <div class="mt-8 bg-surface-canvas border-l-2 border-border-strong p-4 text-xs text-ink-muted leading-relaxed">
                <strong class="text-ink-primary block mb-1">Non-Negotiable Regulatory &amp; Advisory Notice:</strong>
                {{ $article->disclaimer ?? 'This guide is provided for educational and due diligence verification purposes only. It does not constitute formal legal counsel, chartered accountancy certification, or financial advice. Franchise laws, municipal zoning regulations, and state GST provisions vary. Always engage independent legal and financial professionals before executing binding contracts or transferring franchise capital.' }}
            </div>
        </div>

        <!-- Next Steps / Navigation Card -->
        <div class="bg-surface-paper border border-border-subtle rounded-sm p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h4 class="font-serif text-lg font-bold text-ink-primary mb-1">
                    Ready to evaluate audited opportunities?
                </h4>
                <p class="text-xs text-ink-secondary">
                    Browse verified franchise profiles with itemized CapEx and independent evidence checks.
                </p>
            </div>
            <div class="flex items-center gap-3 whitespace-nowrap">
                <x-ui.button href="{{ route('opportunities.index') }}" variant="secondary" class="text-xs">
                    Browse Directory
                </x-ui.button>
                <x-ui.button href="{{ route('enquiry.create') }}" variant="primary" class="text-xs">
                    Assisted Search
                </x-ui.button>
            </div>
        </div>
    </div>
</x-layout.app>
