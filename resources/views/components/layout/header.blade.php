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
                <a href="{{ route('guides.index') }}" class="hover:text-ink-primary transition-colors">
                    Due Diligence Guides
                </a>
                <a href="{{ url('/assessment') }}" class="hover:text-ink-primary transition-colors">
                    Matching Engine
                </a>
                <a href="{{ route('assistant.index') }}" class="hover:text-ink-primary transition-colors flex items-center gap-1.5">
                    <span>AI Discovery</span>
                    <span class="text-[10px] font-mono px-1 py-0.2 bg-surface-paper border border-border-subtle rounded-xs text-status-verified-text">
                        Grounded
                    </span>
                </a>
                <a href="{{ url('/how-it-works') }}" class="hover:text-ink-primary transition-colors">
                    How It Works
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-ink-primary transition-colors font-semibold text-ink-primary">
                        My Dashboard
                    </a>
                @endauth
            </nav>

            <!-- Actions / Auth Area -->
            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}" class="text-sm font-medium text-ink-secondary hover:text-ink-primary transition-colors">
                        Sign In
                    </a>
                    <a href="{{ url('/enquiry') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-ink-primary hover:bg-accent-navy-light rounded-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-ink-primary focus:ring-offset-2">
                        Get Started
                    </a>
                @else
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-mono text-ink-muted hidden sm:inline">
                            {{ Auth::user()->name }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-ink-muted hover:text-ink-primary hover:underline cursor-pointer">
                                Sign Out
                            </button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</header>
