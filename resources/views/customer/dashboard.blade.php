<x-layout.app title="Entrepreneur Dashboard — Franchise Dekho">
    <!-- Dashboard Header -->
    <section class="bg-surface-paper border-b border-border-subtle py-8 md:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                            Entrepreneur Portal
                        </span>
                        <x-ui.badge variant="neutral" size="sm">
                            Role: {{ ucfirst($user->role) }}
                        </x-ui.badge>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        Welcome, {{ $user->name }}
                    </h1>
                    <p class="text-xs text-ink-muted mt-1">
                        {{ $user->email }} &bull; {{ $user->phone ?? 'Phone unlisted' }} &bull; Target Focus: <strong class="text-ink-secondary">{{ $user->city ?? 'N/A' }}, {{ $user->state ?? 'IN' }}</strong>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <x-ui.button href="{{ route('assessment.index') }}" variant="secondary" class="!py-2 !px-4 !text-xs">
                        Algorithmic Matcher
                    </x-ui.button>
                    <x-ui.button href="{{ route('opportunities.index') }}" variant="primary" class="!py-2 !px-4 !text-xs">
                        Browse Opportunities &rarr;
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if (session('status'))
            <x-ui.alert variant="success">
                {{ session('status') }}
            </x-ui.alert>
        @endif

        <!-- Quick Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-surface-canvas p-5 border border-border-subtle rounded-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted block mb-1">Total Enquiries</span>
                <span class="font-serif text-3xl font-bold text-ink-primary">{{ $metrics['total_enquiries'] }}</span>
                <span class="text-[11px] text-ink-muted block mt-1">Submitted through platform</span>
            </div>

            <div class="bg-surface-canvas p-5 border border-border-subtle rounded-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-accent-green block mb-1">Active Coordinated Cases</span>
                <span class="font-serif text-3xl font-bold text-accent-green">{{ $metrics['active_cases'] }}</span>
                <span class="text-[11px] text-ink-muted block mt-1">Assigned to case management</span>
            </div>

            <div class="bg-surface-canvas p-5 border border-border-subtle rounded-sm">
                <span class="text-xs font-semibold uppercase tracking-wider text-ink-muted block mb-1">Brand Deliveries</span>
                <span class="font-serif text-3xl font-bold text-ink-primary">{{ $metrics['submitted_to_brands'] }}</span>
                <span class="text-[11px] text-ink-muted block mt-1">Consented submissions delivered</span>
            </div>
        </div>

        <!-- Enquiries & Application History -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-serif text-xl font-bold text-ink-primary">
                        Your Coordinated Applications &amp; Case Records
                    </h2>
                    <p class="text-xs text-ink-muted">
                        Every enquiry is assigned a dedicated case manager who validates opportunity territory and guides the intake process.
                    </p>
                </div>
                <a href="{{ route('enquiry.create') }}" class="text-xs font-semibold text-ink-primary hover:underline">
                    + New Needs Assessment
                </a>
            </div>

            @if($enquiries->count() > 0)
                <div class="space-y-4">
                    @foreach($enquiries as $enquiry)
                        <div class="bg-surface-canvas border border-border-subtle rounded-sm p-5 sm:p-6 hover:border-ink-primary transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-border-subtle">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono text-xs font-bold text-ink-primary px-2 py-0.5 bg-surface-paper border border-border-subtle rounded-sm">
                                            {{ $enquiry->reference_code }}
                                        </span>
                                        @if($enquiry->status === 'new')
                                            <x-ui.badge variant="warning" size="sm">Case Specialist Assigned &bull; Reviewing</x-ui.badge>
                                        @elseif($enquiry->status === 'shortlisted')
                                            <x-ui.badge variant="verified" size="sm">Shortlisted for Consultation</x-ui.badge>
                                        @elseif($enquiry->status === 'consented')
                                            <x-ui.badge variant="verified" size="sm">Customer Sharing Consented</x-ui.badge>
                                        @elseif($enquiry->status === 'submitted_to_brand')
                                            <x-ui.badge variant="verified" size="sm">Delivered to Franchisor</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="neutral" size="sm">{{ ucfirst($enquiry->status) }}</x-ui.badge>
                                        @endif
                                    </div>

                                    <h3 class="font-serif text-lg font-bold text-ink-primary">
                                        @if($enquiry->opportunity)
                                            <a href="{{ route('opportunities.show', $enquiry->opportunity->slug) }}" class="hover:underline">
                                                {{ $enquiry->opportunity->title }}
                                            </a>
                                            <span class="text-xs font-normal text-ink-muted">({{ $enquiry->opportunity->company->brand_name }})</span>
                                        @else
                                            <span>Assisted Opportunity Search &bull; {{ ucfirst(str_replace('-', ' ', $enquiry->budget_range)) }}</span>
                                        @endif
                                    </h3>
                                </div>

                                <div class="text-left sm:text-right text-xs text-ink-muted">
                                    <span>Submitted: {{ $enquiry->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-3 text-xs">
                                <div>
                                    <span class="text-ink-muted block text-[10px] uppercase tracking-wider">Target Territory</span>
                                    <span class="font-medium text-ink-primary">{{ $enquiry->target_city }}, {{ $enquiry->target_state }}</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-[10px] uppercase tracking-wider">Allocated Capital</span>
                                    <span class="font-medium text-ink-primary">{{ strtoupper($enquiry->budget_range) }}</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-[10px] uppercase tracking-wider">Space Status</span>
                                    <span class="font-medium text-ink-primary">{{ ucfirst($enquiry->commercial_space_status) }} ({{ $enquiry->carpet_area_sqft ? $enquiry->carpet_area_sqft.' sqft' : 'Flexible' }})</span>
                                </div>
                                <div>
                                    <span class="text-ink-muted block text-[10px] uppercase tracking-wider">Assigned Specialist</span>
                                    <span class="font-medium text-accent-green">{{ $enquiry->assignedCaseManager?->name ?? 'Specialist Assignment in Progress' }}</span>
                                </div>
                            </div>

                            @if($enquiry->customerVisibleUpdates->count() > 0)
                                <div class="my-3 p-3 bg-accent-green-bg/30 border border-status-verified-border rounded-sm space-y-2">
                                    <span class="font-semibold text-accent-green uppercase tracking-wider text-[10px] block">
                                        Milestones &amp; Updates from Your Case Specialist:
                                    </span>
                                    @foreach($enquiry->customerVisibleUpdates as $update)
                                        <div class="text-xs text-ink-secondary">
                                            <div class="flex items-center justify-between text-[11px] text-ink-muted mb-0.5">
                                                <strong class="text-ink-primary">{{ $update->subject ?? 'Coordination Update' }}</strong>
                                                <span class="font-mono text-[10px]">{{ $update->created_at->format('d M Y, H:i') }}</span>
                                            </div>
                                            <p class="leading-relaxed">{{ $update->message }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="pt-3 border-t border-border-subtle flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-ink-muted">
                                <p class="flex items-center gap-1.5">
                                    <span class="text-accent-green font-bold">&bull;</span>
                                    Next Action: Our assigned case specialist coordinates territory viability with the franchisor before scheduling preliminary intake.
                                </p>
                                @if($enquiry->opportunity)
                                    <a href="{{ route('opportunities.show', $enquiry->opportunity->slug) }}" class="font-semibold text-ink-primary hover:underline">
                                        View Disclosures &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-surface-canvas p-10 border border-border-subtle rounded-sm text-center">
                    <div class="max-w-md mx-auto space-y-3">
                        <h3 class="font-serif text-lg font-bold text-ink-primary">
                            No Enquiries Submitted Yet
                        </h3>
                        <p class="text-xs text-ink-muted leading-relaxed">
                            You haven't initiated a managed application or assisted search yet. Explore our directory or submit your needs assessment to receive coordinated support.
                        </p>
                        <div class="pt-2 flex justify-center gap-3">
                            <x-ui.button href="{{ route('opportunities.index') }}" variant="secondary" class="!py-2 !px-4 !text-xs">
                                Browse Verified Brands
                            </x-ui.button>
                            <x-ui.button href="{{ route('enquiry.create') }}" variant="primary" class="!py-2 !px-4 !text-xs">
                                Start Needs Assessment
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Managed Platform Policy Notice -->
        <div class="bg-surface-paper/70 p-5 border border-border-subtle rounded-sm text-xs text-ink-muted leading-relaxed space-y-2">
            <h4 class="font-semibold text-ink-secondary uppercase tracking-wider text-[11px]">
                Customer Privacy &amp; Coordination Standard
            </h4>
            <p>
                Franchise Dekho does not sell customer contact records to third-party marketing brokers. Your documents and contact information are shared with a franchisor or brand only after your explicit confirmation. Franchise Dekho does not guarantee franchisor acceptance, territory allocation, or business profitability.
            </p>
        </div>
    </div>
</x-layout.app>
