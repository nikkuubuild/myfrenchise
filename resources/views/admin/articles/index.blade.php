<x-layout.app title="Editorial Guides & Content Freshness — Admin Desk" robots="noindex, nofollow">
    <!-- Admin Sub-Navigation -->
    <div class="bg-surface-paper border-b border-border-subtle py-3 text-xs text-ink-muted">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="font-semibold text-ink-primary">Staff Operations:</span>
                <a href="{{ route('admin.cases.index') }}" class="hover:text-ink-primary">Case Management</a>
                <span class="text-ink-muted">&bull;</span>
                <a href="{{ route('admin.grievances.index') }}" class="hover:text-ink-primary">Grievances</a>
                <span class="text-ink-muted">&bull;</span>
                <a href="{{ route('admin.billing.index') }}" class="hover:text-ink-primary">Billing & Revenue</a>
                <span class="text-ink-muted">&bull;</span>
                <a href="{{ route('admin.articles.index') }}" class="font-medium text-ink-primary underline">Editorial & SEO</a>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
                <span>Editorial Freshness Queue</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-ink-primary">Editorial Content & Freshness</h1>
                <p class="text-sm text-ink-secondary mt-1">
                    Manage due diligence guides, statutory references, and regulatory re-verification timestamps (180-day SLA).
                </p>
            </div>
            <div>
                <x-ui.button href="{{ route('admin.articles.create') }}" variant="primary" class="text-xs">
                    + New Editorial Guide
                </x-ui.button>
            </div>
        </div>

        @if(session('status'))
            <div class="bg-accent-green-bg border border-status-verified-border text-status-verified-text px-4 py-3 rounded-xs text-xs mb-6 flex items-center gap-2">
                <span class="font-bold">&check;</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Articles Table -->
        <div class="bg-surface-canvas border border-border-subtle rounded-sm shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-surface-paper border-b border-border-subtle text-ink-muted uppercase tracking-wider font-mono">
                            <th class="py-3 px-4">Guide Title & Sector</th>
                            <th class="py-3 px-4">Author</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Freshness & Last Audit</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle text-ink-secondary">
                        @forelse($articles as $art)
                            <tr class="hover:bg-surface-paper transition-colors">
                                <td class="py-3 px-4">
                                    <a href="{{ route('guides.show', $art->slug) }}" target="_blank" class="font-serif font-bold text-ink-primary hover:underline block text-sm">
                                        {{ $art->title }} &nearr;
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="font-mono text-ink-muted">Slug: /guides/{{ $art->slug }}</span>
                                        @if($art->category)
                                            &bull; <span class="text-accent-green font-medium">{{ $art->category->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-medium text-ink-primary block">{{ $art->author_name }}</span>
                                    <span class="text-ink-muted">{{ $art->reading_time_minutes }} min read</span>
                                </td>
                                <td class="py-3 px-4">
                                    @if($art->is_published)
                                        <x-ui.badge variant="verified">Published</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="draft">Draft</x-ui.badge>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($art->isFresh())
                                        <div class="flex items-center gap-1 text-status-verified-text font-medium">
                                            <span>&check; Compliant Fresh</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 text-status-disputed-text font-medium">
                                            <span>&excl; Audit Overdue (>180d)</span>
                                        </div>
                                    @endif
                                    <div class="text-ink-muted mt-0.5 font-mono">
                                        {{ $art->last_verified_at ? $art->last_verified_at->format('d M Y') : 'Unverified' }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <form method="POST" action="{{ route('admin.articles.verify', $art) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 text-xs font-mono font-medium bg-surface-paper hover:bg-surface-canvas border border-border-subtle rounded-xs text-status-verified-text transition-colors">
                                                Re-verify Today
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.articles.edit', $art) }}" class="px-2.5 py-1 text-xs font-mono font-medium bg-surface-paper hover:bg-surface-canvas border border-border-subtle rounded-xs text-ink-primary transition-colors">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-ink-muted">
                                    No editorial guides recorded. Click "+ New Editorial Guide" above to author initial compliance research.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($articles->hasPages())
                <div class="p-4 border-t border-border-subtle">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout.app>
