@props(['navItems'])

<aside
    x-data="{ collapsed: (localStorage.getItem('sidebar-collapsed') === '1') }"
    x-effect="localStorage.setItem('sidebar-collapsed', collapsed ? '1' : '0')"
    :style="collapsed ? 'width: var(--sidebar-width-collapsed)' : 'width: var(--sidebar-width)'"
    class="app-sidebar hidden shrink-0 flex-col gap-5 p-4 transition-[width] duration-200 ease-standard lg:flex"
>
    <div class="sidebar-brand">
        <div class="sidebar-brand-mark">
            <i data-lucide="clipboard-check" class="size-5" aria-hidden="true"></i>
        </div>
        <div x-show="!collapsed" x-transition.opacity.duration.120ms class="min-w-0">
            <img src="{{ asset('images/bakoftec-logo.png') }}" alt="Bakof Tec" class="sidebar-logo">
            <p>Suíte RH · Avaliações</p>
        </div>
    </div>

    <nav class="flex flex-1 flex-col gap-1" aria-label="Navegação principal">
        @foreach ($navItems as [$label, $routeName, $icon])
            <a
                href="{{ route($routeName) }}"
                title="{{ $label }}"
                @if (request()->routeIs($routeName)) aria-current="page" @endif
                class="sidebar-nav-item {{ request()->routeIs($routeName) ? 'is-active' : '' }}"
            >
                <i data-lucide="{{ $icon }}" class="size-4 shrink-0" aria-hidden="true"></i>
                <span x-show="!collapsed" x-transition.opacity.duration.120ms class="truncate">{{ $label }}</span>
            </a>
        @endforeach
    </nav>

    <button
        type="button"
        @click="collapsed = !collapsed"
        class="sidebar-nav-item sidebar-collapse"
        :aria-label="collapsed ? 'Expandir menu' : 'Recolher menu'"
    >
        <i data-lucide="menu" class="size-4 shrink-0" aria-hidden="true"></i>
        <span x-show="!collapsed" x-transition.opacity.duration.120ms>Recolher</span>
    </button>
</aside>
