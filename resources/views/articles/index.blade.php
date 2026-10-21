<x-layout.app 
    title="Franchise Due Diligence & Regulatory Guides — Franchise Dekho"
    metaDescription="Authoritative legal, financial, and operational guides for evaluating franchise and dealership opportunities across India. Independent evidence research."
    :canonical="route('guides.index')"
>
    <!-- Breadcrumbs -->
    <x-ui.breadcrumbs :items="[
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Due Diligence Guides']
    ]">
        <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
        <span class="text-xs text-ink-muted">Editorial Research & Statutory Citations</span>
    </x-ui.breadcrumbs>

    <!-- Header Section -->
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                        Knowledge Hub & Educational Due Diligence
                    </span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-ink-primary mb-4">
                    Franchise Due Diligence & Legal Guides
                </h1>
                <p class="text-base text-ink-secondary leading-relaxed">
                    Practical, legally grounded research to help prospective franchisees evaluate contract terms, capital requirements, statutory licenses, and territorial rights under Indian law.
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content & Articles Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Category Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-8 border-b border-border-subtle">
            <a href="{{ route('guides.index') }}" 
               class="px-3 py-1.5 text-xs font-medium rounded-xs border transition-colors whitespace-nowrap {{ empty($selectedCategory) ? 'bg-ink-primary text-white border-ink-primary' : 'bg-surface-canvas text-ink-secondary border-border-subtle hover:bg-surface-paper' }}">
                All Topics
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('guides.index', ['category' => $cat->slug]) }}" 
                   class="px-3 py-1.5 text-xs font-medium rounded-xs border transition-colors whitespace-nowrap {{ $selectedCategory === $cat->slug ? 'bg-ink-primary text-white border-ink-primary' : 'bg-surface-canvas text-ink-secondary border-border-subtle hover:bg-surface-paper' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($articles as $article)
                    <article class="bg-surface-canvas border border-border-subtle rounded-sm flex flex-col justify-between hover:border-ink-muted transition-colors shadow-xs">
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                @if($article->category)
                                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                                        {{ $article->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted">
                                        Regulatory Compliance
                                    </span>
                                @endif
                                <span class="text-xs font-mono text-ink-muted">
                                    {{ $article->reading_time_minutes }} min read
                                </span>
                            </div>

                            <h2 class="font-serif text-xl font-bold text-ink-primary mb-3 hover:text-accent-navy transition-colors">
                                <a href="{{ route('guides.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            <p class="text-xs text-ink-secondary leading-relaxed mb-4 line-clamp-3">
                                {{ $article->excerpt }}
                            </p>
                        </div>

                        <div class="px-6 py-4 bg-surface-paper border-t border-border-subtle flex items-center justify-between text-xs text-ink-muted">
                            <span class="truncate">By {{ $article->author_name }}</span>
                            @if($article->last_verified_at)
                                <span class="font-mono text-status-verified-text">
                                    Verified: {{ $article->last_verified_at->format('M Y') }}
                                </span>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        @else
            <div class="bg-surface-paper border border-border-subtle rounded-sm p-12 text-center max-w-xl mx-auto">
                <p class="text-sm text-ink-muted mb-4">No published guides match this filter.</p>
                <a href="{{ route('guides.index') }}" class="text-xs font-semibold text-accent-navy hover:underline">
                    View All Editorial Guides
                </a>
            </div>
        @endif
    </div>
</x-layout.app>
