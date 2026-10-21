<x-layout.app title="Grievance Docket {{ $grievance->tracking_code }} — Staff Operations" robots="noindex, nofollow">
    <section class="bg-surface-paper border-b border-border-subtle py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('admin.grievances.index') }}" class="text-xs font-mono font-semibold uppercase tracking-wider text-ink-muted hover:text-ink-primary">
                            &larr; Grievance Queue
                        </a>
                        <span class="text-xs text-ink-muted">&bull;</span>
                        <span class="font-mono text-xs font-bold text-ink-primary">{{ $grievance->tracking_code }}</span>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        {{ $grievance->subject }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    @if ($grievance->priority === 'urgent')
                        <span class="px-2.5 py-1 bg-red-100 text-red-800 text-xs font-mono font-semibold uppercase rounded-xs border border-red-300">
                            Urgent Fraud Alert
                        </span>
                    @endif

                    @if ($grievance->status === 'received')
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Received
                        </span>
                    @elseif ($grievance->status === 'under_investigation')
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Under Investigation
                        </span>
                    @elseif ($grievance->status === 'resolved')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Formally Resolved
                        </span>
                    @else
                        <span class="px-3 py-1 bg-stone-100 text-stone-800 text-xs font-semibold uppercase rounded-xs">
                            Status: Rejected / Closed
                        </span>
                    @endif
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Complaint Dossier -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Reporter & Opportunity Header -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h2 class="font-serif text-lg font-bold text-ink-primary border-b border-border-subtle pb-3">
                        Complainant &amp; Target Information
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block font-semibold">Complainant Identity</span>
                            <div class="font-semibold text-ink-primary text-sm mt-0.5">{{ $grievance->full_name }}</div>
                            <div class="text-ink-secondary mt-0.5">{{ $reporterTypes[$grievance->reporter_type] ?? $grievance->reporter_type }}</div>
                            <div class="font-mono text-ink-muted mt-1">{{ $grievance->email }} &bull; {{ $grievance->phone }}</div>
                        </div>

                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block font-semibold">Associated Opportunity / Brand</span>
                            @if ($grievance->opportunity)
                                <div class="font-semibold text-ink-primary text-sm mt-0.5">{{ $grievance->opportunity->title }}</div>
                                <div class="text-ink-secondary mt-0.5">{{ $grievance->opportunity->company?->legal_name }}</div>
                                <div class="mt-1">
                                    <a href="{{ route('opportunities.show', $grievance->opportunity->slug) }}" target="_blank" class="text-accent-green hover:underline">
                                        View Public Listing &rarr;
                                    </a>
                                </div>
                            @else
                                <div class="text-ink-muted mt-0.5">Platform-Wide / Unspecified Entity</div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-border-subtle grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Category:</span>
                            <strong class="text-accent-navy-light">{{ $categories[$grievance->category] ?? $grievance->category }}</strong>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Filed Timestamp:</span>
                            <span class="font-mono text-ink-primary">{{ $grievance->created_at->format('M d, Y - H:i') }} IST</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Recorded IP:</span>
                            <span class="font-mono text-ink-muted">{{ $grievance->ip_address ?? 'Not recorded / Anonymized' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Statement of Facts -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary">
                        Statement of Facts &amp; Description
                    </h3>
                    <div class="p-4 bg-surface-paper border border-border-subtle rounded-xs whitespace-pre-line text-xs font-mono text-ink-primary leading-relaxed">
                        {{ $grievance->description }}
                    </div>

                    @if ($grievance->evidence_summary)
                        <div class="pt-2 space-y-2">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-ink-muted">Evidence Summary Provided by Complainant</h4>
                            <div class="p-3 bg-surface-paper border border-border-subtle rounded-xs text-xs text-ink-secondary leading-relaxed font-sans">
                                {{ $grievance->evidence_summary }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Resolution Dossier -->
                @if ($grievance->resolution_notes)
                    <div class="bg-emerald-50/40 border border-emerald-300 rounded-sm p-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-serif text-base font-bold text-emerald-950">
                                Official Statutory Resolution Findings
                            </h3>
                            @if ($grievance->resolved_at)
                                <span class="font-mono text-xs text-emerald-800">
                                    Resolved on: {{ $grievance->resolved_at->format('d M Y - H:i') }}
                                </span>
                            @endif
                        </div>
                        <div class="p-4 bg-surface-canvas border border-emerald-200 rounded-xs text-xs text-ink-primary whitespace-pre-line leading-relaxed">
                            {{ $grievance->resolution_notes }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Col: Operational Actions -->
            <div class="space-y-6">
                <!-- Assignment Card -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary">
                        Case Assignment
                    </h3>
                    <p class="text-xs text-ink-muted">
                        Assign this docket to a designated Compliance or Case Officer.
                    </p>

                    <form method="POST" action="{{ route('admin.grievances.assign', $grievance) }}" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label for="assigned_to_user_id" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Designated Officer
                            </label>
                            <select id="assigned_to_user_id" name="assigned_to_user_id" required class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                                <option value="">Select Officer...</option>
                                @foreach ($officers as $officer)
                                    <option value="{{ $officer->id }}" {{ $grievance->assigned_to_user_id === $officer->id ? 'selected' : '' }}>
                                        {{ $officer->name }} ({{ ucfirst(str_replace('_', ' ', $officer->role)) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full py-2 bg-ink-primary text-white rounded-xs font-semibold hover:bg-accent-navy-light cursor-pointer">
                            Assign Docket
                        </button>
                    </form>
                </div>

                <!-- Status Update Card -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary">
                        Update Status &amp; Priority
                    </h3>

                    <form method="POST" action="{{ route('admin.grievances.status', $grievance) }}" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label for="status" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Status</label>
                            <select id="status" name="status" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                                <option value="received" {{ $grievance->status === 'received' ? 'selected' : '' }}>Received</option>
                                <option value="under_investigation" {{ $grievance->status === 'under_investigation' ? 'selected' : '' }}>Under Investigation</option>
                                <option value="resolved" {{ $grievance->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="rejected" {{ $grievance->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>

                        <div>
                            <label for="priority" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Priority</label>
                            <select id="priority" name="priority" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                                <option value="normal" {{ $grievance->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ $grievance->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $grievance->priority === 'urgent' ? 'selected' : '' }}>Urgent Fraud</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2 bg-surface-paper border border-border-strong text-ink-primary rounded-xs font-semibold hover:bg-surface-canvas cursor-pointer">
                            Update Investigation State
                        </button>
                    </form>
                </div>

                <!-- Formal Resolution Form -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary">
                        Statutory Resolution Order
                    </h3>
                    <p class="text-xs text-ink-muted">
                        Record final findings, enforcement actions taken against unauthorized parties, and conclude the investigation docket.
                    </p>

                    <form method="POST" action="{{ route('admin.grievances.resolve', $grievance) }}" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label for="outcome" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Final Outcome</label>
                            <select id="outcome" name="outcome" required class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                                <option value="resolved">Formally Resolved (Remedial Action Taken)</option>
                                <option value="rejected">Rejected / Unsubstantiated Claim</option>
                            </select>
                        </div>

                        <div>
                            <label for="resolution_notes" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                Resolution Findings &amp; Orders <span class="text-red-600">*</span>
                            </label>
                            <textarea id="resolution_notes" name="resolution_notes" rows="4" required minlength="10" placeholder="Detail verified findings: brand contacted, listing suspended, broker blacklisted, or lack of substantiating evidence..." class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary leading-relaxed">{{ old('resolution_notes', $grievance->resolution_notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2 bg-accent-green text-white rounded-xs font-semibold hover:opacity-90 cursor-pointer shadow-sm">
                            Submit Official Resolution &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
