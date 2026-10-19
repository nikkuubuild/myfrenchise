<x-layout.app title="Track Grievance / Fraud Investigation — Franchise Dekho">
    <section class="bg-surface-paper border-b border-border-subtle py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green block mb-2">
                    Case Transparency &bull; Statutory Oversight
                </span>
                <h1 class="font-serif text-3xl md:text-5xl font-bold text-ink-primary mb-4">
                    Track Complaint Status
                </h1>
                <p class="text-base text-ink-secondary leading-relaxed">
                    Check the live investigation progress and statutory resolution findings of your complaint using your unique complaint code and email address.
                </p>
            </div>
        </div>
    </section>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
        <!-- Lookup Form -->
        <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 sm:p-8">
            <form method="GET" action="{{ route('grievance.track') }}" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label for="tracking_code" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                            Tracking Code <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="tracking_code" name="tracking_code" value="{{ request('tracking_code') }}" required placeholder="e.g. FD-GRV-202610-ABCD" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs font-mono text-ink-primary uppercase focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                    </div>

                    <div>
                        <label for="email" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                            Filing Email Address <span class="text-red-600">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ request('email') }}" required placeholder="Email used during filing" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary focus:ring-1 focus:ring-ink-primary focus:border-ink-primary">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <span class="text-[11px] text-ink-muted">
                        Confidentiality: Email verification is required to access complaint findings.
                    </span>
                    <button type="submit" class="px-5 py-2 bg-ink-primary text-white text-xs font-semibold rounded-sm hover:bg-accent-navy-light cursor-pointer shadow-sm">
                        Lookup Status &rarr;
                    </button>
                </div>
            </form>
        </div>

        @if ($searched && ! $grievance)
            <x-ui.alert variant="warning">
                <div class="space-y-1">
                    <strong class="block">No Record Located</strong>
                    <p class="text-xs">
                        We could not find a grievance matching tracking code <strong>"{{ request('tracking_code') }}"</strong> and email <strong>"{{ request('email') }}"</strong>. Please verify the credentials against your filing receipt.
                    </p>
                </div>
            </x-ui.alert>
        @endif

        @if ($grievance)
            <!-- Grievance Dossier Card -->
            <div class="bg-surface-canvas border border-border-strong rounded-sm overflow-hidden shadow-xs">
                <!-- Header -->
                <div class="bg-surface-paper p-6 border-b border-border-subtle flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-sm font-bold text-ink-primary">{{ $grievance->tracking_code }}</span>
                            @if ($grievance->priority === 'urgent')
                                <span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-mono font-semibold uppercase rounded-xs border border-red-300">Urgent</span>
                            @endif
                        </div>
                        <h2 class="font-serif text-lg font-bold text-ink-primary">
                            {{ $grievance->subject }}
                        </h2>
                    </div>

                    <div>
                        @if ($grievance->status === 'received')
                            <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-amber-800 border border-amber-300 text-xs font-semibold uppercase tracking-wider rounded-xs">
                                Docket Received
                            </span>
                        @elseif ($grievance->status === 'under_investigation')
                            <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-800 border border-blue-300 text-xs font-semibold uppercase tracking-wider rounded-xs">
                                Under Investigation
                            </span>
                        @elseif ($grievance->status === 'resolved')
                            <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-300 text-xs font-semibold uppercase tracking-wider rounded-xs">
                                Formally Resolved
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-stone-100 text-stone-800 border border-stone-300 text-xs font-semibold uppercase tracking-wider rounded-xs">
                                Closed / Rejected
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Timeline / Milestones -->
                <div class="p-6 border-b border-border-subtle bg-surface-canvas">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-ink-muted mb-4">Statutory Milestone Progress</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-3 bg-surface-paper border border-border-subtle rounded-xs">
                            <span class="text-accent-green font-semibold block mb-1">&check; 1. Complaint Registered</span>
                            <span class="text-ink-secondary text-[11px] block">{{ $grievance->created_at->format('M d, Y - H:i') }} IST</span>
                            <span class="text-[10px] text-ink-muted font-mono mt-1 block">24h SLA Met</span>
                        </div>

                        <div class="p-3 bg-surface-paper border {{ in_array($grievance->status, ['under_investigation', 'resolved', 'rejected']) ? 'border-border-strong' : 'border-border-subtle opacity-60' }} rounded-xs">
                            <span class="{{ in_array($grievance->status, ['under_investigation', 'resolved', 'rejected']) ? 'text-accent-navy-light font-semibold' : 'text-ink-muted' }} block mb-1">
                                {{ in_array($grievance->status, ['under_investigation', 'resolved', 'rejected']) ? '●' : '○' }} 2. Officer Assigned &amp; In Review
                            </span>
                            <span class="text-ink-secondary text-[11px] block">
                                {{ $grievance->assignedOfficer ? 'Assigned to '.$grievance->assignedOfficer->name : 'Pending Assignment' }}
                            </span>
                        </div>

                        <div class="p-3 bg-surface-paper border {{ in_array($grievance->status, ['resolved', 'rejected']) ? 'border-accent-green bg-accent-green-bg/20' : 'border-border-subtle opacity-60' }} rounded-xs">
                            <span class="{{ in_array($grievance->status, ['resolved', 'rejected']) ? 'text-status-verified-text font-semibold' : 'text-ink-muted' }} block mb-1">
                                {{ in_array($grievance->status, ['resolved', 'rejected']) ? '&check;' : '○' }} 3. Official Resolution
                            </span>
                            <span class="text-ink-secondary text-[11px] block">
                                {{ $grievance->resolved_at ? $grievance->resolved_at->format('M d, Y') : 'Pending Final Orders' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Details & Resolution Findings -->
                <div class="p-6 space-y-6 text-xs text-ink-secondary leading-relaxed">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <span class="text-ink-muted block uppercase text-[10px] font-semibold">Violation Category</span>
                            <strong class="text-ink-primary">{{ $categories[$grievance->category] ?? $grievance->category }}</strong>
                        </div>
                        <div>
                            <span class="text-ink-muted block uppercase text-[10px] font-semibold">Associated Opportunity</span>
                            <strong class="text-ink-primary">{{ $grievance->opportunity?->title ?? 'Platform General / Unspecified' }}</strong>
                        </div>
                    </div>

                    <div>
                        <span class="text-ink-muted block uppercase text-[10px] font-semibold mb-1">Summary of Facts Submitted</span>
                        <div class="p-3 bg-surface-paper border border-border-subtle rounded-xs whitespace-pre-line text-ink-primary font-mono text-[11px]">
                            {{ $grievance->description }}
                        </div>
                    </div>

                    @if ($grievance->resolution_notes)
                        <div class="p-4 bg-emerald-50/50 border border-emerald-200 rounded-sm space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-emerald-900 uppercase tracking-wider text-[11px]">
                                    Official Statutory Resolution Findings
                                </span>
                                @if ($grievance->resolved_at)
                                    <span class="text-[10px] font-mono text-emerald-700">Resolved: {{ $grievance->resolved_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                            <p class="text-emerald-950 whitespace-pre-line text-xs font-sans">
                                {{ $grievance->resolution_notes }}
                            </p>
                        </div>
                    @else
                        <div class="p-4 bg-surface-paper border border-border-subtle rounded-sm text-ink-muted text-xs">
                            <p>
                                <em>Investigation is currently underway. Our designated compliance team is examining primary evidence and engaging corresponding parties. The statutory resolution report will appear here once finalized within 15 working days.</em>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layout.app>
