@props([
    'label',
    'value',
    'icon' => 'activity',
    'tone' => 'default', // default | warning | danger | success
    'hint' => null,
    'href' => null,
])

@php
    $toneIcon = [
        'default' => 'text-primary',
        'warning' => 'text-warning',
        'danger' => 'text-danger',
        'success' => 'text-success',
    ][$tone] ?? 'text-primary';

    $toneBg = [
        'default' => 'bg-surface-hover',
        'warning' => 'bg-warning-background',
        'danger' => 'bg-danger-background',
        'success' => 'bg-success-background',
    ][$tone] ?? 'bg-surface-hover';

    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'app-card p-5 flex flex-col gap-4']) }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-foreground-muted">{{ $label }}</p>
        <span class="metric-icon {{ $toneBg }} {{ $toneIcon }}">
            <i data-lucide="{{ $icon }}" class="size-4" aria-hidden="true"></i>
        </span>
    </div>

    <p class="text-3xl font-semibold tracking-tight text-foreground">{{ $value }}</p>

    @if ($hint)
        <p class="text-sm text-foreground-muted">{{ $hint }}</p>
    @endif

    {{ $slot }}
</{{ $tag }}>
