<footer class="bg-surface-paper border-t border-border-subtle mt-20" aria-labelledby="footer-heading">
    <h2 id="footer-heading" class="sr-only">Footer</h2>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Col 1: Platform Purpose -->
            <div class="md:col-span-2 space-y-4">
                <span class="font-serif font-bold text-lg text-ink-primary">Franchise Dekho</span>
                <p class="text-sm text-ink-muted leading-relaxed max-w-md">
                    India-wide managed business-opportunity discovery and application-coordination platform. We combine independent verification research with dedicated human case management.
                </p>
                <div class="pt-2 text-xs font-mono text-ink-muted">
                    Regulated Coordination &bull; Strict Verification Workflows &bull; Consented Sharing
                </div>
            </div>

            <!-- Col 2: Discovery -->
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-primary mb-3">Discovery</h3>
                <ul class="space-y-2 text-sm text-ink-secondary">
                    <li><a href="{{ url('/opportunities') }}" class="hover:text-ink-primary transition-colors">All Opportunities</a></li>
                    <li><a href="{{ route('guides.index') }}" class="hover:text-ink-primary transition-colors">Due Diligence Guides</a></li>
                    <li><a href="{{ url('/how-it-works') }}" class="hover:text-ink-primary transition-colors">How It Works</a></li>
                    <li><a href="{{ url('/enquiry') }}" class="hover:text-ink-primary transition-colors">Assisted Search</a></li>
                </ul>
            </div>

            <!-- Col 3: Compliance & Legal -->
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-primary mb-3">Governance & Policies</h3>
                <ul class="space-y-2 text-sm text-ink-secondary">
                    <li><a href="{{ url('/verification-policy') }}" class="hover:text-ink-primary transition-colors">Verification Standard</a></li>
                    <li><a href="{{ url('/grievance-and-fraud') }}" class="hover:text-ink-primary transition-colors">Grievance & Fraud Reporting</a></li>
                    <li><a href="{{ url('/privacy-policy') }}" class="hover:text-ink-primary transition-colors">Privacy Notice</a></li>
                    <li><a href="{{ url('/terms-of-service') }}" class="hover:text-ink-primary transition-colors">Terms of Service</a></li>
                    <li><a href="{{ route('pages.cookie-policy') }}" class="hover:text-ink-primary transition-colors">Cookie Policy</a></li>
                </ul>
            </div>
        </div>

        <!-- Mandatory Editorial Legal Disclaimer Bar -->
        <div class="mt-12 pt-8 border-t border-border-subtle">
            <div class="bg-surface-canvas p-4 rounded-sm border border-border-subtle text-xs text-ink-muted leading-relaxed mb-6">
                <p class="font-medium text-ink-secondary mb-1">Non-Negotiable Regulatory & Advisory Notice:</p>
                Franchise Dekho is an independent managed discovery and application coordination platform. We do not provide franchise broker licenses, loan guarantees, or legal approvals. The respective company, franchisor, financial institution, or government regulator retains sole approval authority over all applications. We explicitly do not guarantee business success, approval, turnover, profit margins, or territory exclusivity beyond verified vendor terms.
            </div>
            
            <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-ink-muted gap-4">
                <p>&copy; {{ date('Y') }} Franchise Dekho. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <span>Hostinger Shared Optimized</span>
                    <span>&bull;</span>
                    <a href="{{ url('/sitemap.xml') }}" class="hover:underline">XML Sitemap</a>
                    <span>&bull;</span>
                    <a href="{{ url('/health') }}" class="hover:underline">System Status</a>
                </div>
            </div>
        </div>
    </div>
</footer>
