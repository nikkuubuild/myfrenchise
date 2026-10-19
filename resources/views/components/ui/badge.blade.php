@props([
    'variant' => 'verified', // verified, pending, caveat, rejected, neutral
    'size' => 'md',
])

@php
$variantClasses = match($variant) {
    'verified' => 'bg-status-verified-bg text-status-verified-text border-status-verified-border',
    'pending' => 'bg-status-pending-bg text-status-pending-text border-status-pending-border',
    'caveat' => 'bg-status-caveat-bg text-status-caveat-text border-status-caveat-border',
    'rejected' => 'bg-status-rejected-bg text-status-rejected-text border-status-rejected-border',
    default => 'bg-surface-subtle text-ink-secondary border-border-subtle',
};

$sizeClasses = match($size) {
    'sm' => 'text-[11px] px-1.5 py-0.5',
    default => 'text-xs px-2.5 py-1',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium rounded-xs border {$variantClasses} {$sizeClasses}"]) }}>
    @if($variant === 'verified')
        <svg class="w-3.5 h-3.5 text-status-verified-text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    @elseif($variant === 'caveat')
        <svg class="w-3.5 h-3.5 text-status-caveat-text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    @endif
    {{ $slot }}
</span>
