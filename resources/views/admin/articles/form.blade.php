<x-layout.app :title="($isEdit ? 'Edit Guide: ' . $article->title : 'Author New Editorial Guide') . ' — Admin Desk'" robots="noindex, nofollow">
    <!-- Admin Sub-Navigation -->
    <div class="bg-surface-paper border-b border-border-subtle py-3 text-xs text-ink-muted">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.articles.index') }}" class="hover:text-ink-primary">&larr; Back to Editorial Queue</a>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-status-verified-text"></span>
                <span>Editorial Workflow</span>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="font-serif text-3xl font-bold text-ink-primary">
                {{ $isEdit ? 'Edit Editorial Guide' : 'Author New Editorial Guide' }}
            </h1>
            <p class="text-sm text-ink-secondary mt-1">
                Grounded due diligence research, statutory acts, and verifiable legal principles under Indian law.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-surface-paper border-l-4 border-status-disputed-border p-4 mb-6 rounded-xs">
                <div class="text-xs font-semibold text-status-disputed-text mb-1">Please correct the following errors:</div>
                <ul class="list-disc list-inside text-xs text-ink-secondary space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $isEdit ? route('admin.articles.update', $article) : route('admin.articles.store') }}" class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-xs space-y-5">
                <!-- Title & Slug -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="title" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            Article Title *
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                    <div>
                        <label for="slug" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            URL Slug (leave empty to generate)
                        </label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $article->slug) }}" placeholder="e.g. franchise-due-diligence-checklist" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                </div>

                <!-- Sector Category & Reading Time -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            Sector Category (Optional)
                        </label>
                        <select id="category_id" name="category_id" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                            <option value="">General Due Diligence & Legal</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="reading_time_minutes" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            Reading Time (Minutes) *
                        </label>
                        <input type="number" id="reading_time_minutes" name="reading_time_minutes" value="{{ old('reading_time_minutes', $article->reading_time_minutes ?? 5) }}" min="1" max="120" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                </div>

                <!-- Author Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="author_name" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            Author / Research Entity *
                        </label>
                        <input type="text" id="author_name" name="author_name" value="{{ old('author_name', $article->author_name ?? 'Franchise Dekho Research Desk') }}" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                    <div>
                        <label for="author_title" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            Author Designation *
                        </label>
                        <input type="text" id="author_title" name="author_title" value="{{ old('author_title', $article->author_title ?? 'Senior Regulatory & Franchise Due Diligence Analyst') }}" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                </div>

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                        Executive Summary / Excerpt *
                    </label>
                    <textarea id="excerpt" name="excerpt" rows="2" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">{{ old('excerpt', $article->excerpt) }}</textarea>
                </div>

                <!-- Main Content -->
                <div>
                    <label for="content" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                        Full Guide Content *
                    </label>
                    <textarea id="content" name="content" rows="12" required class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary font-mono">{{ old('content', $article->content) }}</textarea>
                </div>

                <!-- Statutory Sources & Evidence -->
                <div>
                    <label for="sources_and_evidence" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                        Statutory References & Acts Cited
                    </label>
                    <textarea id="sources_and_evidence" name="sources_and_evidence" rows="3" placeholder="e.g. Consumer Protection Act 2019, Section 2(47); Companies Act 2013; FSSAI Gazette Order 2021..." class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary font-mono">{{ old('sources_and_evidence', $article->sources_and_evidence) }}</textarea>
                </div>

                <!-- Non-promotional Legal Disclaimer -->
                <div>
                    <label for="disclaimer" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                        Editorial Disclaimer
                    </label>
                    <textarea id="disclaimer" name="disclaimer" rows="2" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">{{ old('disclaimer', $article->disclaimer ?? 'This guide is provided for educational and due diligence verification purposes only. It does not constitute formal legal counsel, chartered accountancy certification, or financial advice.') }}</textarea>
                </div>

                <!-- SEO Meta Overrides -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-border-subtle">
                    <div>
                        <label for="meta_title" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            SEO Meta Title
                        </label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $article->meta_title) }}" placeholder="e.g. Franchise Due Diligence Checklist 2026 | Franchise Dekho" class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                    <div>
                        <label for="meta_description" class="block text-xs font-mono uppercase tracking-wider text-ink-muted mb-1">
                            SEO Meta Description
                        </label>
                        <input type="text" id="meta_description" name="meta_description" value="{{ old('meta_description', $article->meta_description) }}" placeholder="Brief summary for search engine snippet..." class="w-full text-sm bg-surface-canvas border border-border-strong rounded-xs px-3 py-2 text-ink-primary focus:border-ink-primary focus:ring-1 focus:ring-ink-primary">
                    </div>
                </div>

                <!-- Publication Checkbox -->
                <div class="flex items-center gap-2 pt-4 border-t border-border-subtle">
                    <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $article->is_published ?? true) ? 'checked' : '' }} class="rounded-xs border-border-strong text-ink-primary focus:ring-ink-primary">
                    <label for="is_published" class="text-xs font-medium text-ink-primary">
                        Publish to Public Knowledge Hub & XML Sitemap
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.articles.index') }}" class="text-xs font-mono text-ink-muted hover:text-ink-primary">
                    Cancel & Return
                </a>
                <x-ui.button type="submit" variant="primary">
                    {{ $isEdit ? 'Update Editorial Guide' : 'Publish Editorial Guide' }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layout.app>
