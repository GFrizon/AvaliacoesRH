@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($eyebrow)
            <p class="page-kicker">{{ $eyebrow }}</p>
        @endif
        <h2 class="page-title mt-1">{{ $title }}</h2>
        @if ($description)
            <p class="mt-2 max-w-2xl text-sm text-foreground-muted">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            {{ $actions }}
        </div>
    @endisset
</div>
