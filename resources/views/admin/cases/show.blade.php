<x-layout.app title="Case Workspace #{{ $enquiry->reference_code }} — Franchise Dekho Operations">
    <!-- Header -->
    <section class="bg-surface-paper border-b border-border-subtle py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <a href="{{ route('admin.cases.index') }}" class="text-xs text-ink-muted hover:text-ink-primary hover:underline">
                            &larr; Back to Pipeline
                        </a>
                        <span class="text-xs text-border-strong">&bull;</span>
                        <span class="font-mono text-xs font-bold text-ink-primary">{{ $enquiry->reference_code }}</span>
                        <x-ui.badge variant="{{ in_array($enquiry->stage, ['submitted_to_brand', 'closed_won']) ? 'verified' : ($enquiry->stage === 'new' ? 'warning' : 'neutral') }}" size="sm">
                            {{ $stages[$enquiry->stage] ?? ucfirst($enquiry->stage) }}
                        </x-ui.badge>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        {{ $enquiry->full_name }} &bull; {{ $enquiry->opportunity ? $enquiry->opportunity->company->brand_name : 'Assisted Discovery' }}
                    </h1>
                    <p class="text-xs text-ink-muted mt-1">
                        Submitted: {{ $enquiry->created_at->format('d M Y, h:i A') }} &bull;
                        Assigned: <strong class="text-ink-secondary">{{ $enquiry->assignedCaseManager?->name ?? 'Unassigned' }}</strong> &bull;
                        Priority: <strong class="uppercase text-ink-primary font-mono">{{ $enquiry->priority }}</strong>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @if($enquiry->opportunity)
                        <x-ui.button href="{{ route('opportunities.show', $enquiry->opportunity->slug) }}" variant="secondary" target="_blank" class="!py-2 !px-4 !text-xs">
                            View Public Listing &rarr;
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('status'))
            <div class="mb-6">
                <x-ui.alert variant="success">
                    {{ session('status') }}
                </x-ui.alert>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Dossier, Checklist, Communications -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Applicant Profile & Criteria Dossier -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-sm">
                    <h2 class="font-serif text-lg font-bold text-ink-primary mb-4 pb-2 border-b border-border-subtle">
                        Applicant Profile &amp; Capacity Dossier
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6">
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Full Legal Name</span>
                            <span class="font-semibold text-ink-primary text-sm">{{ $enquiry->full_name }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Phone &amp; Mobile</span>
                            <span class="font-mono text-ink-primary">{{ $enquiry->phone }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Email Address</span>
                            <span class="text-ink-primary">{{ $enquiry->email }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Target Location</span>
                            <span class="font-semibold text-ink-primary">{{ $enquiry->target_city }}, {{ $enquiry->target_state }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Allocated Capital Outlay</span>
                            <span class="font-mono font-bold text-accent-green">{{ strtoupper($enquiry->budget_range) }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Preferred Timeline</span>
                            <span class="text-ink-primary capitalize">{{ str_replace('-', ' ', $enquiry->preferred_timeline) }}</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Commercial Space Status</span>
                            <span class="text-ink-primary capitalize">{{ $enquiry->commercial_space_status }} ({{ $enquiry->carpet_area_sqft ? $enquiry->carpet_area_sqft.' sqft' : 'Flexible' }})</span>
                        </div>
                        <div>
                            <span class="text-ink-muted uppercase tracking-wider text-[10px] block">Entrepreneur Background</span>
                            <span class="text-ink-primary capitalize">{{ str_replace('_', ' ', $enquiry->business_experience) }}</span>
                        </div>
                    </div>

                    @if($enquiry->notes)
                        <div class="bg-surface-paper/70 p-3 rounded-sm border border-border-subtle text-xs text-ink-secondary mb-4">
                            <span class="font-semibold text-ink-primary block mb-1">Customer Supplied Objectives:</span>
                            {{ $enquiry->notes }}
                        </div>
                    @endif

                    <!-- Regulatory Consent Proof -->
                    <div class="bg-accent-green-bg/30 p-3 rounded-sm border border-status-verified-border text-[11px] text-ink-secondary flex items-start gap-2">
                        <span class="text-status-verified-text font-bold">&check;</span>
                        <div>
                            <strong class="text-status-verified-text">Mandatory Data-Sharing Consent Recorded:</strong>
                            Granted on {{ $enquiry->consent_recorded_at?->format('d M Y, H:i:s') ?? 'N/A' }}
                            (IP: <span class="font-mono">{{ $enquiry->ip_address ?? 'Direct' }}</span>).
                            Customer authorized Franchise Dekho to coordinate with verified brands.
                        </div>
                    </div>
                </div>

                <!-- Operational Action Checklist -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-border-subtle">
                        <h2 class="font-serif text-lg font-bold text-ink-primary">
                            Operational Tasks &amp; Checklist
                        </h2>
                        <span class="text-xs font-mono text-ink-muted">
                            {{ $enquiry->tasks->where('is_completed', true)->count() }} / {{ $enquiry->tasks->count() }} Complete
                        </span>
                    </div>

                    <!-- Tasks List -->
                    <div class="space-y-2 mb-6">
                        @forelse($enquiry->tasks as $task)
                            <div class="flex items-start justify-between gap-3 p-3 bg-surface-paper/50 rounded-sm border border-border-subtle hover:bg-surface-paper transition-colors">
                                <form method="POST" action="{{ route('admin.cases.tasks.toggle', $task->id) }}" class="flex items-start gap-3 flex-grow">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="mt-0.5 h-4 w-4 rounded-sm border border-border-strong flex items-center justify-center cursor-pointer {{ $task->is_completed ? 'bg-status-verified-text text-white border-status-verified-text' : 'bg-white' }}">
                                        @if($task->is_completed)
                                            <span class="text-[10px] font-bold">&check;</span>
                                        @endif
                                    </button>
                                    <div class="text-xs">
                                        <span class="{{ $task->is_completed ? 'line-through text-ink-muted' : 'font-medium text-ink-primary' }}">
                                            {{ $task->title }}
                                        </span>
                                        <div class="text-[10px] text-ink-muted mt-0.5">
                                            @if($task->due_date)
                                                <span>Due: {{ $task->due_date->format('d M Y') }}</span> &bull;
                                            @endif
                                            @if($task->assignee)
                                                <span>Assigned to: {{ $task->assignee->name }}</span>
                                            @endif
                                            @if($task->is_completed && $task->completedBy)
                                                &bull; <span class="text-status-verified-text font-medium">Completed by {{ $task->completedBy->name }} ({{ $task->completed_at?->format('d M') }})</span>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <p class="text-xs text-ink-muted italic py-3">No tasks added to checklist yet.</p>
                        @endforelse
                    </div>

                    <!-- Add New Task Form -->
                    <form method="POST" action="{{ route('admin.cases.tasks.store', $enquiry->id) }}" class="pt-4 border-t border-border-subtle flex flex-col sm:flex-row gap-2">
                        @csrf
                        <input type="text" name="title" placeholder="New operational action item..." required
                               class="text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary flex-grow">
                        <input type="date" name="due_date"
                               class="text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                        <button type="submit" class="bg-ink-primary text-white text-xs font-medium px-4 py-2 rounded-sm hover:bg-accent-navy-light shrink-0">
                            + Add Task
                        </button>
                    </form>
                </div>

                <!-- Confidential Documents & Sharing Consents Section -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-2 border-b border-border-subtle">
                        <div>
                            <h2 class="font-serif text-lg font-bold text-ink-primary">
                                Confidential Documents &amp; Sharing Authorizations
                            </h2>
                            <p class="text-xs text-ink-muted">
                                Private storage with auditable access logging. Documents require explicit customer consent before external sharing.
                            </p>
                        </div>
                        <span class="text-xs font-mono text-ink-muted">
                            {{ $enquiry->documents->count() }} Files in Vault
                        </span>
                    </div>

                    <!-- Document Repository Table -->
                    <div class="space-y-3">
                        @forelse($enquiry->documents as $doc)
                            <div class="p-3.5 bg-surface-paper/40 border border-border-subtle rounded-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-ink-primary">{{ $doc->title }}</span>
                                        <x-ui.badge variant="{{ $doc->verification_status === 'verified' ? 'verified' : ($doc->verification_status === 'rejected' ? 'danger' : 'neutral') }}" size="sm">
                                            {{ ucfirst($doc->verification_status) }}
                                        </x-ui.badge>
                                        <span class="text-[10px] font-mono uppercase px-1.5 py-0.5 bg-surface-canvas border border-border-subtle text-ink-muted rounded-sm">
                                            {{ $doc->category }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-ink-muted">
                                        <span class="font-mono">{{ $doc->original_filename }}</span> ({{ $doc->formattedSize() }}) &bull;
                                        Uploaded by: {{ $doc->uploader?->name ?? 'System' }} ({{ $doc->created_at->format('d M Y') }})
                                        @if($doc->verifier)
                                            &bull; Verified by {{ $doc->verifier->name }} ({{ $doc->verified_at?->format('d M') }})
                                        @endif
                                    </p>
                                    @if($doc->notes)
                                        <p class="text-[11px] text-ink-secondary italic">Note: {{ $doc->notes }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('admin.cases.documents.download', $doc->id) }}" class="px-2.5 py-1.5 bg-surface-canvas border border-border-strong text-ink-primary hover:border-ink-primary rounded-sm text-xs font-semibold">
                                        Download &darr;
                                    </a>

                                    @if($doc->verification_status !== 'verified')
                                        <form method="POST" action="{{ route('admin.cases.documents.verify', $doc->id) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="verification_status" value="verified">
                                            <button type="submit" class="px-2.5 py-1.5 bg-accent-green-bg border border-status-verified-border text-status-verified-text rounded-sm text-xs font-semibold hover:bg-emerald-100">
                                                Verify
                                            </button>
                                        </form>
                                    @endif

                                    @if($doc->verification_status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.cases.documents.verify', $doc->id) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="verification_status" value="rejected">
                                            <button type="submit" class="px-2.5 py-1.5 bg-red-50 border border-red-200 text-red-700 rounded-sm text-xs font-medium hover:bg-red-100">
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-ink-muted italic py-2">No documents stored in this case vault yet.</p>
                        @endforelse
                    </div>

                    <!-- Staff Upload Form -->
                    <form method="POST" action="{{ route('admin.cases.documents.store', $enquiry->id) }}" enctype="multipart/form-data" class="pt-4 border-t border-border-subtle grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                        @csrf
                        <input type="text" name="title" placeholder="Document Title..." required
                               class="bg-surface-canvas border border-border-strong rounded-sm px-3 py-1.5 text-ink-primary">
                        <select name="category" required class="bg-surface-canvas border border-border-strong rounded-sm px-3 py-1.5 text-ink-primary">
                            <option value="identity">Identity</option>
                            <option value="financial">Financial</option>
                            <option value="property">Property</option>
                            <option value="brand_disclosure">Brand Disclosure</option>
                            <option value="other">Other</option>
                        </select>
                        <input type="file" name="document" accept=".pdf,.png,.jpg,.jpeg" required
                               class="bg-surface-canvas border border-border-strong rounded-sm px-2 py-1 text-ink-primary text-xs file:mr-1 file:py-0.5 file:px-1.5 file:rounded-sm file:border-0 file:text-[10px]">
                        <button type="submit" class="bg-ink-primary text-white font-medium px-3 py-1.5 rounded-sm hover:bg-accent-navy-light text-center">
                            + Upload File
                        </button>
                    </form>

                    <!-- Active Document Consents Table & Creator -->
                    <div class="pt-6 border-t border-border-subtle space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-serif text-sm font-bold text-ink-primary">
                                Document-Sharing Consent Authorizations
                            </h3>
                            <span class="text-[11px] text-ink-muted">Customer permission required prior to external delivery</span>
                        </div>

                        @if($enquiry->consents->count() > 0)
                            <div class="space-y-2">
                                @foreach($enquiry->consents as $consent)
                                    <div class="p-3 bg-surface-paper/40 border border-border-subtle rounded-sm flex items-start justify-between gap-3 text-xs">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <strong class="text-ink-primary">{{ $consent->opportunity?->company?->brand_name ?? 'Target Brand' }}</strong>
                                                <x-ui.badge variant="{{ $consent->status === 'granted' ? 'verified' : ($consent->status === 'pending' ? 'warning' : 'neutral') }}" size="sm">
                                                    {{ ucfirst($consent->status) }}
                                                </x-ui.badge>
                                                <span class="text-[10px] text-ink-muted">
                                                    {{ count($consent->included_document_ids ?? []) }} Document(s) Included
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-ink-secondary leading-relaxed">{{ $consent->scope_summary }}</p>
                                            <span class="text-[10px] text-ink-muted block mt-1 font-mono">
                                                Requested by {{ $consent->requester?->name ?? 'Staff' }} on {{ $consent->created_at->format('d M Y') }}
                                                @if($consent->granted_at)
                                                    &bull; Authorized by Customer on {{ $consent->granted_at->format('d M Y, H:i') }} (IP: {{ $consent->ip_address }})
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Create New Consent Request Form (Staff) -->
                        @if($enquiry->documents->count() > 0 && $enquiry->user)
                            <div class="p-4 bg-surface-paper/60 border border-border-subtle rounded-sm space-y-3">
                                <span class="font-semibold text-ink-primary uppercase tracking-wider text-[11px] block">
                                    Request Customer Authorization for Brand Submission Pack
                                </span>
                                <form method="POST" action="{{ route('admin.cases.consents.request', $enquiry->id) }}" class="space-y-3 text-xs">
                                    @csrf

                                    <div>
                                        <label class="block font-semibold uppercase tracking-wider text-ink-muted mb-1.5">
                                            Select Documents to Include:
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($enquiry->documents as $cDoc)
                                                <label class="flex items-center gap-2 p-2 bg-surface-canvas border border-border-subtle rounded-sm cursor-pointer">
                                                    <input type="checkbox" name="document_ids[]" value="{{ $cDoc->id }}" class="rounded-sm border-border-strong text-ink-primary focus:ring-ink-primary">
                                                    <div>
                                                        <span class="font-medium text-ink-primary block">{{ $cDoc->title }}</span>
                                                        <span class="text-[10px] text-ink-muted uppercase font-mono">{{ $cDoc->category }} ({{ $cDoc->formattedSize() }})</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label for="scope_summary" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">
                                            Scope Summary &amp; Purpose Statement:
                                        </label>
                                        <textarea id="scope_summary" name="scope_summary" rows="2" required placeholder="Describe the purpose of this disclosure pack (e.g. Submitting identity and financial capacity records to Chai Point franchise team for preliminary territory review)..."
                                                  class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary"></textarea>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-ink-primary text-white font-medium px-4 py-2 rounded-sm hover:bg-accent-navy-light transition-colors text-xs">
                                            Submit Consent Request to Applicant &rarr;
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Communications & Notes Timeline -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-6 shadow-sm">
                    <h2 class="font-serif text-lg font-bold text-ink-primary mb-4 pb-2 border-b border-border-subtle">
                        Case Activity, Communications &amp; Audit Trail
                    </h2>

                    <!-- Log New Communication / Note Form -->
                    <form method="POST" action="{{ route('admin.cases.communications.store', $enquiry->id) }}" class="mb-8 p-4 bg-surface-paper/60 border border-border-subtle rounded-sm space-y-4">
                        @csrf
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-ink-primary">Record Note or Communication</span>
                            <div class="flex items-center gap-4 text-xs">
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="type" value="internal_note" checked class="text-ink-primary focus:ring-ink-primary">
                                    <span class="text-ink-secondary">Internal Staff Note (Confidential)</span>
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="type" value="customer_update" class="text-accent-green focus:ring-accent-green">
                                    <span class="text-accent-green font-semibold">Customer Milestone Update</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <input type="text" name="subject" placeholder="Subject / Headline (Optional)"
                                   class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                        </div>

                        <div>
                            <textarea name="message" rows="3" required placeholder="Enter detailed operational notes, call summary, or customer update..."
                                      class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary"></textarea>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-ink-muted text-[11px]">
                                Customer Milestone Updates appear directly on the customer's portal dashboard.
                            </span>
                            <button type="submit" class="bg-ink-primary text-white font-medium px-4 py-2 rounded-sm hover:bg-accent-navy-light transition-colors">
                                Post Entry &rarr;
                            </button>
                        </div>
                    </form>

                    <!-- Timeline List -->
                    <div class="space-y-4">
                        @forelse($enquiry->communications as $comm)
                            <div class="p-4 rounded-sm border {{ $comm->is_customer_visible ? 'border-status-verified-border bg-accent-green-bg/20' : 'border-border-subtle bg-surface-paper/40' }}">
                                <div class="flex items-center justify-between gap-2 mb-2 pb-2 border-b border-border-subtle/60 text-[11px]">
                                    <div class="flex items-center gap-2">
                                        @if($comm->is_customer_visible)
                                            <x-ui.badge variant="verified" size="sm">Customer Visible Update</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="neutral" size="sm">Staff Confidential Note</x-ui.badge>
                                        @endif
                                        @if($comm->subject)
                                            <strong class="text-ink-primary">{{ $comm->subject }}</strong>
                                        @endif
                                    </div>
                                    <span class="font-mono text-ink-muted">{{ $comm->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-xs text-ink-secondary leading-relaxed whitespace-pre-line">{{ $comm->message }}</p>
                                <span class="text-[10px] text-ink-muted block mt-2">Recorded by: {{ $comm->author?->name ?? 'System' }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-ink-muted italic py-2">No notes or communications logged yet.</p>
                        @endforelse

                        <!-- Status History Trail -->
                        @if($enquiry->statusHistories->count() > 0)
                            <div class="mt-8 pt-6 border-t border-border-subtle">
                                <h3 class="font-serif text-sm font-bold text-ink-primary mb-3">Status Progression Audit History</h3>
                                <div class="space-y-2 text-xs">
                                    @foreach($enquiry->statusHistories as $history)
                                        <div class="flex items-start justify-between gap-3 py-1.5 border-b border-border-subtle/50 text-[11px]">
                                            <div>
                                                <span class="font-semibold text-ink-primary">{{ $history->from_status }}</span>
                                                <span class="text-ink-muted">&rarr;</span>
                                                <span class="font-semibold text-accent-green">{{ $history->to_status }}</span>
                                                @if($history->comment)
                                                    <p class="text-ink-secondary italic mt-0.5">"{{ $history->comment }}"</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-ink-muted shrink-0 font-mono">
                                                <span>{{ $history->created_at->format('d M Y, H:i') }}</span>
                                                <span class="block text-[10px]">by {{ $history->user?->name ?? 'System' }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Quick Control Cards -->
            <div class="space-y-6">
                <!-- Stage Transition Card -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-5 shadow-sm space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary pb-2 border-b border-border-subtle">
                        Progress Case Stage
                    </h3>

                    <form method="POST" action="{{ route('admin.cases.status', $enquiry->id) }}" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label for="stage" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Target Stage</label>
                            <select id="stage" name="stage" required class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                                @foreach($stages as $key => $label)
                                    <option value="{{ $key }}" @selected($enquiry->stage === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="comment" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Audit Comment (Optional)</label>
                            <textarea id="comment" name="comment" rows="2" placeholder="Rationale for stage transition..."
                                      class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-ink-primary text-white font-medium py-2 px-3 rounded-sm hover:bg-accent-navy-light transition-colors text-center">
                            Update Stage &rarr;
                        </button>
                    </form>
                </div>

                <!-- Assignment Card -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-5 shadow-sm space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary pb-2 border-b border-border-subtle">
                        Case Ownership
                    </h3>

                    <form method="POST" action="{{ route('admin.cases.assign', $enquiry->id) }}" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label for="assigned_to_user_id" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Assign To Specialist</label>
                            <select id="assigned_to_user_id" name="assigned_to_user_id" required class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                                <option value="">Select Specialist</option>
                                @foreach($caseManagers as $cm)
                                    <option value="{{ $cm->id }}" @selected($enquiry->assigned_to_user_id == $cm->id)>
                                        {{ $cm->name }} ({{ ucfirst($cm->role) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <textarea name="comment" rows="2" placeholder="Assignment note..."
                                      class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary"></textarea>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="submit" class="w-full bg-ink-primary text-white font-medium py-2 px-3 rounded-sm hover:bg-accent-navy-light transition-colors text-center">
                                Save Assignment
                            </button>
                            @if($enquiry->assigned_to_user_id !== Auth::id())
                                <form method="POST" action="{{ route('admin.cases.assign', $enquiry->id) }}">
                                    @csrf
                                    <input type="hidden" name="assigned_to_user_id" value="{{ Auth::id() }}">
                                    <button type="submit" class="py-2 px-3 bg-surface-paper border border-border-strong text-ink-secondary hover:text-ink-primary rounded-sm text-center shrink-0">
                                        Claim
                                    </button>
                                </form>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Priority & Follow-up Schedule Card -->
                <div class="bg-surface-canvas border border-border-subtle rounded-sm p-5 shadow-sm space-y-4">
                    <h3 class="font-serif text-base font-bold text-ink-primary pb-2 border-b border-border-subtle">
                        Priority &amp; Schedule
                    </h3>

                    <form method="POST" action="{{ route('admin.cases.followup', $enquiry->id) }}" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label for="priority" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Case Priority</label>
                            <select id="priority" name="priority" required class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                                <option value="normal" @selected($enquiry->priority === 'normal')>Normal</option>
                                <option value="high" @selected($enquiry->priority === 'high')>High Priority</option>
                                <option value="urgent" @selected($enquiry->priority === 'urgent')>Urgent Escalation</option>
                            </select>
                        </div>

                        <div>
                            <label for="next_follow_up_at" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Next Follow-up Due</label>
                            <input type="date" id="next_follow_up_at" name="next_follow_up_at" value="{{ $enquiry->next_follow_up_at?->format('Y-m-d') }}"
                                   class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                        </div>

                        <button type="submit" class="w-full bg-ink-primary text-white font-medium py-2 px-3 rounded-sm hover:bg-accent-navy-light transition-colors text-center">
                            Save Schedule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
