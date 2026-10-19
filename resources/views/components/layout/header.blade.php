<header class="border-b border-border-subtle bg-surface-canvas sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Brand Identity -->
            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-ink-primary hover:opacity-90 transition-opacity">
                    <span class="font-serif font-bold text-xl tracking-tight text-ink-primary">Franchise Dekho</span>
                    <span class="hidden md:inline-block text-xs font-mono uppercase tracking-wider px-2 py-0.5 bg-accent-green-bg text-status-verified-text rounded-sm border border-status-verified-border">
                        Managed Platform
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-ink-secondary" aria-label="Main Navigation">
                <a href="{{ url('/opportunities') }}" class="hover:text-ink-primary transition-colors">
                    Explore Opportunities
                </a>
                <a href="{{ url('/how-it-works') }}" class="hover:text-ink-primary transition-colors">
                    How Managed Service Works
                </a>
                <a href="{{ url('/verification-policy') }}" class="hover:text-ink-primary transition-colors">
                    Verification Standard
                </a>
            </nav>

            <!-- Primary Action -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/enquiry') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-ink-primary hover:bg-accent-navy-light rounded-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-ink-primary focus:ring-offset-2">
                    Start Needs Assessment
                </a>
            </div>
        </div>
    </div>
</header>
