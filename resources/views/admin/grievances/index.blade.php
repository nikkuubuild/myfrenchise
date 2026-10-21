<x-layout.app title="Grievance Redressal &amp; Fraud Queue — Staff Operations" robots="noindex, nofollow">
    <section class="bg-surface-paper border-b border-border-subtle py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-status-rejected-text">
                            Statutory Compliance &bull; IT Rules 2021
                        </span>
                        <x-ui.badge variant="verified" size="sm">Staff Queue</x-ui.badge>
                    </div>
                    <h1 class="font-serif text-2xl md:text-3xl font-bold text-ink-primary">
                        Grievance Redressal &amp; Fraud Investigation Queue
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.cases.index') }}" class="text-xs font-medium px-3 py-1.5 bg-surface-canvas border border-border-strong rounded-sm text-ink-primary hover:border-ink-primary">
                        &larr; Switch to Case Pipeline
                    </a>
                    <a href="{{ route('admin.grievances.index', ['status' => 'received']) }}" class="text-xs font-medium px-3 py-1.5 bg-ink-primary text-white rounded-sm hover:bg-accent-navy-light shadow-sm">
                        Pending Intake ({{ $metrics['received'] }})
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

        <!-- Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-ink-muted block mb-1">Total Grievances</span>
                <span class="font-serif text-2xl font-bold text-ink-primary">{{ $metrics['total'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-700 block mb-1">New Intake</span>
                <span class="font-serif text-2xl font-bold text-amber-700">{{ $metrics['received'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-accent-navy-light block mb-1">In Investigation</span>
                <span class="font-serif text-2xl font-bold text-accent-navy-light">{{ $metrics['under_investigation'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-accent-green block mb-1">Formally Resolved</span>
                <span class="font-serif text-2xl font-bold text-accent-green">{{ $metrics['resolved'] }}</span>
            </div>
            <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-red-700 block mb-1">Urgent / Fraud Alerts</span>
                <span class="font-serif text-2xl font-bold text-red-700">{{ $metrics['urgent'] }}</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-surface-canvas p-4 border border-border-subtle rounded-sm">
            <form method="GET" action="{{ route('admin.grievances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end text-xs">
                <div>
                    <label for="q" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Search Docket</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Tracking #, name, or subject..." class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                </div>

                <div>
                    <label for="status" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Investigation Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                        <option value="all">All Statuses</option>
                        <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received / Unassigned</option>
                        <option value="under_investigation" {{ request('status') === 'under_investigation' ? 'selected' : '' }}>Under Investigation</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Priority Level</label>
                    <select id="priority" name="priority" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    </select>
                </div>

                <div>
                    <label for="category" class="block font-semibold uppercase tracking-wider text-ink-muted mb-1">Category</label>
                    <select id="category" name="category" class="w-full px-3 py-2 bg-surface-paper border border-border-strong rounded-xs text-ink-primary">
                        <option value="">All Categories</option>
                        @foreach ($categories as $catKey => $catLabel)
                            <option value="{{ $catKey }}" {{ request('category') === $catKey ? 'selected' : '' }}>
                                {{ Str::limit($catLabel, 28) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-grow px-4 py-2 bg-ink-primary text-white rounded-xs font-semibold hover:bg-accent-navy-light cursor-pointer">
                        Filter
                    </button>
                    @if (request()->hasAny(['q', 'status', 'priority', 'category']))
                        <a href="{{ route('admin.grievances.index') }}" class="px-3 py-2 bg-surface-paper border border-border-subtle rounded-xs text-ink-muted hover:text-ink-primary">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Queue -->
        <div class="bg-surface-canvas border border-border-subtle rounded-sm overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border-subtle text-xs">
                    <thead class="bg-surface-paper text-ink-primary font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Tracking &amp; Priority</th>
                            <th class="px-4 py-3 text-left">Category &amp; Subject</th>
                            <th class="px-4 py-3 text-left">Reporter</th>
                            <th class="px-4 py-3 text-left">Status &amp; Assigned Officer</th>
                            <th class="px-4 py-3 text-left">Statutory SLA Window</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle">
                        @forelse ($grievances as $item)
                            <tr class="hover:bg-surface-paper/40 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-mono font-bold text-ink-primary">
                                        {{ $item->tracking_code }}
                                    </div>
                                    @if ($item->priority === 'urgent')
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 bg-red-100 text-red-800 text-[10px] font-mono font-semibold uppercase rounded-xs border border-red-300">
                                            Urgent Fraud Alert
                                        </span>
                                    @elseif ($item->priority === 'high')
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 bg-amber-100 text-amber-800 text-[10px] font-mono font-semibold uppercase rounded-xs">
                                            High Priority
                                        </span>
                                    @else
                                        <span class="inline-block mt-0.5 text-[10px] text-ink-muted">Normal Priority</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-[11px] font-semibold text-accent-navy-light uppercase tracking-wider">
                                        {{ $categories[$item->category] ?? $item->category }}
                                    </div>
                                    <div class="font-medium text-ink-primary max-w-sm truncate" title="{{ $item->subject }}">
                                        {{ $item->subject }}
                                    </div>
                                    @if ($item->opportunity)
                                        <div class="text-[10px] text-ink-muted">
                                            Brand: {{ $item->opportunity->title }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-medium text-ink-primary">{{ $item->full_name }}</div>
                                    <div class="text-[11px] text-ink-muted font-mono">{{ $item->email }}</div>
                                    <div class="text-[10px] text-ink-muted">{{ $item->phone }}</div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($item->status === 'received')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Received
                                        </span>
                                    @elseif ($item->status === 'under_investigation')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Investigating
                                        </span>
                                    @elseif ($item->status === 'resolved')
                                        <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Resolved
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 bg-stone-100 text-stone-800 text-[10px] font-semibold uppercase rounded-xs">
                                            Rejected
                                        </span>
                                    @endif

                                    <div class="text-[10px] text-ink-secondary mt-1">
                                        {{ $item->assignedOfficer ? $item->assignedOfficer->name : 'Unassigned' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-[11px]">
                                    <div class="font-mono text-ink-muted">Filed: {{ $item->created_at->format('d M Y') }}</div>
                                    @php
                                        $deadline = $item->created_at->copy()->addDays(15);
                                        $isPastDue = now()->greaterThan($deadline) && ! $item->isResolved();
                                    @endphp
                                    <div class="font-mono {{ $isPastDue ? 'text-red-700 font-bold' : 'text-ink-secondary' }}">
                                        Target: {{ $deadline->format('d M Y') }}
                                        @if ($isPastDue)
                                            <span class="text-[9px] text-red-600 uppercase block">Overdue</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.grievances.show', $item) }}" class="inline-flex items-center px-3 py-1.5 bg-ink-primary text-white text-xs font-medium rounded-xs hover:bg-accent-navy-light transition-colors">
                                        Review Docket &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-ink-muted">
                                    No grievances or fraud alerts match the selected criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($grievances->hasPages())
                <div class="p-4 border-t border-border-subtle bg-surface-paper">
                    {{ $grievances->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout.app>
