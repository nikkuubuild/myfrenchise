@props([
    'items' => [],
])

@php
    $breadcrumbList = [];
    $pos = 1;
    foreach ($items as $item) {
        $breadcrumbList[] = [
            '@type' => 'ListItem',
            'position' => $pos,
            'name' => $item['label'],
            'item' => $item['url'] ?? null,
        ];
        $pos++;
    }
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumbList,
    ];
@endphp

<div class="bg-surface-paper border-b border-border-subtle py-3 text-xs text-ink-muted">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <nav class="flex items-center gap-2 flex-wrap" aria-label="Breadcrumb">
            @foreach($items as $index => $item)
                @if(!$loop->first)
                    <span class="text-ink-muted" aria-hidden="true">&rsaquo;</span>
                @endif

                @if(!empty($item['url']) && !$loop->last)
                    <a href="{{ $item['url'] }}" class="text-ink-secondary hover:text-ink-primary hover:underline transition-colors">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-ink-primary font-medium truncate" aria-current="page">
                        {{ $item['label'] }}
                    </span>
                @endif
            @endforeach
        </nav>

        @if(isset($slot) && !empty(trim($slot)))
            <div class="hidden sm:flex items-center gap-2">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>

@push('schema')
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush
