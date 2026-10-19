@props([
    'variant' => 'primary', // primary, secondary, ghost, danger
    'type' => 'button',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-sm transition-colors focus:outline-none focus:ring-2 focus:ring-ink-primary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed text-sm px-4 py-2.5';

$variantClasses = match($variant) {
    'primary' => 'bg-ink-primary text-white hover:bg-accent-navy-light active:bg-[#0F1D27] shadow-sm',
    'secondary' => 'bg-transparent border border-ink-primary text-ink-primary hover:bg-surface-paper active:bg-surface-subtle',
    'ghost' => 'bg-transparent text-ink-secondary hover:text-ink-primary hover:bg-surface-subtle',
    'danger' => 'bg-status-rejected-text text-white hover:bg-[#6e1818] shadow-sm',
    default => 'bg-ink-primary text-white',
};
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "{$baseClasses} {$variantClasses}"]) }}>
        {{ $slot }}
    </button>
@endif
