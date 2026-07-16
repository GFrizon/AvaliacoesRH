{{--
    Itens de navegação do drawer mobile (components/mobile-drawer.blade.php).
    A sidebar desktop (components/sidebar.blade.php) tem sua própria
    renderização por precisar do comportamento de recolher/expandir, mas
    ambas consomem a mesma variável $navItems definida em layouts/app.blade.php
    — fonte única de verdade para os itens de navegação.
--}}
@foreach ($navItems as [$label, $routeName, $icon])
    <a
        href="{{ route($routeName) }}"
        @if ($closeOnClick ?? false) @click="sidebarOpen = false" @endif
        class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($routeName) ? 'bg-surface-hover text-primary' : 'text-foreground-muted hover:bg-surface-hover hover:text-foreground' }}"
        @if (request()->routeIs($routeName)) aria-current="page" @endif
    >
        <i data-lucide="{{ $icon }}" class="size-4" aria-hidden="true"></i>
        {{ $label }}
    </a>
@endforeach
