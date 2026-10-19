@props([
    'variant' => 'info', // info, warning, success, danger
    'title' => null,
])

@php
$variantClasses = match($variant) {
    'warning' => 'bg-status-caveat-bg text-status-caveat-text border-status-caveat-border',
    'success' => 'bg-status-verified-bg text-status-verified-text border-status-verified-border',
    'danger' => 'bg-status-rejected-bg text-status-rejected-text border-status-rejected-border',
    default => 'bg-[#EFF6FC] text-[#164066] border-[#A8CDED]',
};
@endphp

<div role="alert" {{ $attributes->merge(['class' => "p-4 rounded-sm border text-sm leading-relaxed {$variantClasses}"]) }}>
    @if($title)
        <h4 class="font-semibold mb-1 flex items-center gap-2">
            {{ $title }}
        </h4>
    @endif
    <div>
        {{ $slot }}
    </div>
</div>
