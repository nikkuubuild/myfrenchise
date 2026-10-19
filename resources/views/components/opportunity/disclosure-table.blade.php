@props(['opportunity'])

<div class="overflow-x-auto border border-border-subtle rounded-sm">
    <table class="w-full text-left text-sm border-collapse">
        <thead class="bg-surface-paper border-b border-border-subtle text-xs font-semibold text-ink-primary uppercase tracking-wider">
            <tr>
                <th scope="col" class="py-3 px-4">Item & Description</th>
                <th scope="col" class="py-3 px-4">Disclosed Amount</th>
                <th scope="col" class="py-3 px-4">Type</th>
                <th scope="col" class="py-3 px-4">Provenance / Source Evidence</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-border-subtle bg-surface-canvas text-ink-secondary">
            @forelse($opportunity->financialBreakdowns as $item)
                <tr class="hover:bg-surface-paper/40 transition-colors">
                    <td class="py-3.5 px-4 font-medium text-ink-primary">
                        {{ $item->item_name }}
                    </td>
                    <td class="py-3.5 px-4 font-mono font-medium text-ink-primary">
                        @if($item->amount)
                            {{ \App\Models\Opportunity::formatInr($item->amount) }}
                        @else
                            <span class="text-ink-muted italic">Variable / Unspecified</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-xs">
                        @if($item->is_refundable)
                            <span class="inline-block px-2 py-0.5 bg-accent-green-bg text-status-verified-text rounded-xs font-medium">Refundable</span>
                        @elseif($item->is_estimate)
                            <span class="inline-block px-2 py-0.5 bg-surface-subtle text-ink-muted rounded-xs">Estimate</span>
                        @else
                            <span class="inline-block px-2 py-0.5 bg-surface-paper text-ink-secondary rounded-xs">Fixed Fee</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-xs text-ink-muted font-mono">
                        {{ $item->source_evidence ?? 'Brand Disclosure Schedule' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-4 px-4 text-center text-ink-muted italic">
                        Itemized breakdown schedule pending verified upload.
                    </td>
                </tr>
            @endforelse
            
            <!-- Ongoing Fee / Royalty Row -->
            @if($opportunity->royalty_percentage)
                <tr class="bg-surface-paper/60 font-medium">
                    <td class="py-3 px-4 text-ink-primary">Monthly Ongoing Royalty / Service Fee</td>
                    <td class="py-3 px-4 font-mono text-ink-primary">{{ $opportunity->royalty_percentage }}% of Gross Revenue</td>
                    <td class="py-3 px-4 text-xs text-ink-muted">Ongoing Operating Cost</td>
                    <td class="py-3 px-4 text-xs font-mono text-ink-muted">Standard Operating Agreement</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
