@props(['navItems'])

{{--
    Sidebar desktop: fixa, com opção de recolher (largura persistida em
    localStorage). Em telas menores que "lg" ela não é renderizada aqui;
    o drawer mobile (components/mobile-drawer.blade.php) assume a navegação.
--}}
<aside
    x-data="{ collapsed: (localStorage.getItem('sidebar-collapsed') === '1') }"
    x-effect="localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0')"
    :style="collapsed ? 'width: var(--sidebar-width-collapsed)' : 'width: var(--sidebar-width)'"
    class="app-sidebar hidden shrink-0 flex-col gap-6 p-4 transition-[width] duration-200 ease-standard lg:flex"
>
    <div class="flex items-center gap-3 px-1">
        <div class="grid size-9 shrink-0 place-items-center rounded-md bg-primary text-primary-foreground">
            <i data-lucide="clipboard-check" class="size-5" aria-hidden="true"></i>
        </div>
        <div x-show="!collapsed" x-transition.opacity.duration.120ms class="min-w-0">
            <img src="{{ asset('images/bakoftec-logo.png') }}" alt="Bakof Tec" class="h-8 w-auto max-w-36 object-contain">
            <p class="mt-1 truncate text-xs font-medium uppercase tracking-wide text-foreground-subtle">Suíte RH · Avaliações</p>
        </div>
    </div>

    <nav class="flex flex-1 flex-col gap-1" aria-label="Navegação principal">
        @foreach ($navItems as [$label, $routeName, $icon])
            <a
                href="{{ route($routeName) }}"
                title="{{ $label }}"
                @if (request()->routeIs($routeName)) aria-current="page" @endif
                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($routeName) ? 'bg-surface-hover text-primary' : 'text-foreground-muted hover:bg-surface-hover hover:text-foreground' }}"
            >
                <i data-lucide="{{ $icon }}" class="size-4 shrink-0" aria-hidden="true"></i>
                <span x-show="!collapsed" x-transition.opacity.duration.120ms class="truncate">{{ $label }}</span>
            </a>
        @endforeach
    </nav>

    <button
        type="button"
        @click="collapsed = !collapsed"
        class="btn-ghost justify-start px-3"
        :aria-label="collapsed ? 'Expandir menu' : 'Recolher menu'"
    >
        <i data-lucide="menu" class="size-4 shrink-0" aria-hidden="true"></i>
        <span x-show="!collapsed" x-transition.opacity.duration.120ms>Recolher</span>
    </button>
</aside>
