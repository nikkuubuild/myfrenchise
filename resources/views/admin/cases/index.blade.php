<x-layout.app title="Managed Case Pipeline — Franchise Dekho Operations">
    <section class="bg-surface-paper border-b border-border-subtle py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-accent-green">
                            Operations &bull; Case Management
                        </span>
                        <x-ui.badge variant="verified" size="sm">Staff Access</x-ui.badge>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        Managed Enquiries &amp; Applications Pipeline
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.grievances.index') }}" class="text-xs font-medium px-3 py-1.5 bg-surface-canvas border border-status-rejected-border text-status-rejected-text rounded-sm hover:border-red-700">
                        Grievance &amp; Fraud Queue
                    </a>
                    <a href="{{ route('admin.cases.index', ['assigned' => 'unassigned']) }}" class="text-xs font-medium px-3 py-1.5 bg-surface-canvas border border-border-strong rounded-sm text-ink-primary hover:border-ink-primary">
                        Unassigned Intake ({{ $metrics['unassigned'] }})
                    </a>
                    <a href="{{ route('admin.cases.index', ['assigned' => 'me']) }}" class="text-xs font-medium px-3 py-1.5 bg-ink-primary text-white rounded-sm hover:bg-accent-navy-light shadow-sm">
                        Assigned to Me ({{ $metrics['my_cases'] }})
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @if (session('status'))
            <x-ui.alert variant="success">
                {{ session('status') }}
            </x-ui.alert>
        @endif

        <!-- Pipeline Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted block mb-1">Total Pipeline</span>
                <span class="font-serif text-2xl font-bold text-ink-primary">{{ $metrics['total_cases'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-status-caveat-text block mb-1">Unassigned</span>
                <span class="font-serif text-2xl font-bold text-status-caveat-text">{{ $metrics['unassigned'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-accent-green block mb-1">My Active Cases</span>
                <span class="font-serif text-2xl font-bold text-accent-green">{{ $metrics['my_cases'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-red-700 block mb-1">Urgent Attention</span>
                <span class="font-serif text-2xl font-bold text-red-700">{{ $metrics['urgent'] }}</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
            <form method="GET" action="{{ route('admin.cases.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end text-xs">
                <div>
                    <label for="q" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Search Case</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ref #, name, phone, email..."
                           class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                </div>

                <div>
                    <label for="stage" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Stage</label>
                    <select id="stage" name="stage" class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                        <option value="">All Stages</option>
                        @foreach($stages as $key => $label)
                            <option value="{{ $key }}" @selected(request('stage') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="priority" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Priority</label>
                    <select id="priority" name="priority" class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                        <option value="">All Priorities</option>
                        <option value="normal" @selected(request('priority') === 'normal')>Normal</option>
                        <option value="high" @selected(request('priority') === 'high')>High</option>
                        <option value="urgent" @selected(request('priority') === 'urgent')>Urgent</option>
                    </select>
                </div>

                <div>
                    <label for="assigned_to" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Assigned Specialist</label>
                    <select id="assigned_to" name="assigned_to" class="w-full text-xs bg-surface-canvas border border-border-strong rounded-sm px-3 py-2 text-ink-primary">
                        <option value="">All Staff</option>
                        @foreach($caseManagers as $cm)
                            <option value="{{ $cm->id }}" @selected(request('assigned_to') == $cm->id)>{{ $cm->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full bg-ink-primary text-white font-medium py-2 px-3 rounded-sm hover:bg-accent-navy-light transition-colors text-center">
                        Filter
                    </button>
                    @if(request()->anyFilled(['q', 'stage', 'priority', 'assigned_to', 'assigned']))
                        <a href="{{ route('admin.cases.index') }}" class="py-2 px-3 bg-surface-paper border border-border-strong text-ink-secondary hover:text-ink-primary rounded-sm text-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Case Pipeline Table -->
        <div class="bg-surface-canvas border border-border-subtle rounded-sm overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border-subtle text-left text-xs">
                    <thead class="bg-surface-paper text-ink-muted font-mono uppercase tracking-wider text-[11px]">
                        <tr>
                            <th scope="col" class="py-3.5 px-4 font-semibold">Ref Code &amp; Date</th>
                            <th scope="col" class="py-3.5 px-4 font-semibold">Applicant &amp; Location</th>
                            <th scope="col" class="py-3.5 px-4 font-semibold">Brand / Capital</th>
                            <th scope="col" class="py-3.5 px-4 font-semibold">Stage &amp; Priority</th>
                            <th scope="col" class="py-3.5 px-4 font-semibold">Assigned Specialist</th>
                            <th scope="col" class="py-3.5 px-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle bg-surface-canvas text-ink-secondary">
                        @forelse($enquiries as $case)
                            <tr class="hover:bg-surface-paper/40 transition-colors">
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-ink-primary block">{{ $case->reference_code }}</span>
                                    <span class="text-[11px] text-ink-muted">{{ $case->created_at->format('d M Y') }}</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <strong class="text-ink-primary block">{{ $case->full_name }}</strong>
                                    <span class="text-[11px] text-ink-muted">{{ $case->target_city }}, {{ $case->target_state }}</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($case->opportunity)
                                        <span class="font-medium text-ink-primary block">{{ $case->opportunity->company->brand_name }}</span>
                                    @else
                                        <span class="italic text-ink-muted block">Assisted Search</span>
                                    @endif
                                    <span class="font-mono text-[11px] text-ink-muted">{{ strtoupper($case->budget_range) }}</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5 mb-1">
                                        <x-ui.badge variant="{{ in_array($case->stage, ['submitted_to_brand', 'closed_won']) ? 'verified' : ($case->stage === 'new' ? 'warning' : 'neutral') }}" size="sm">
                                            {{ $stages[$case->stage] ?? ucfirst($case->stage) }}
                                        </x-ui.badge>
                                    </div>
                                    @if($case->priority === 'urgent')
                                        <span class="text-[10px] font-mono font-bold text-red-700 uppercase">&#9888; Urgent</span>
                                    @elseif($case->priority === 'high')
                                        <span class="text-[10px] font-mono text-status-caveat-text uppercase">High Priority</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($case->assignedCaseManager)
                                        <span class="font-medium text-ink-primary">{{ $case->assignedCaseManager->name }}</span>
                                    @else
                                        <span class="text-status-caveat-text italic font-medium">Unassigned</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.cases.show', $case->id) }}" class="font-semibold text-ink-primary hover:underline">
                                        Open Workspace &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-ink-muted italic">
                                    No managed cases found matching the specified filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($enquiries->hasPages())
                <div class="p-4 border-t border-border-subtle">
                    {{ $enquiries->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout.app>
