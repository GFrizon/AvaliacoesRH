@props([
    'variant' => 'neutral', // neutral | success | warning | danger | info
])

@php
    $variants = [
        'neutral' => 'bg-surface-active text-foreground-muted',
        'success' => 'bg-success-background text-success',
        'warning' => 'bg-warning-background text-warning',
        'danger' => 'bg-danger-background text-danger',
        'info' => 'bg-info-background text-info',
    ];
    $classes = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "status-pill {$classes}"]) }}>
    {{ $slot }}
</span>
