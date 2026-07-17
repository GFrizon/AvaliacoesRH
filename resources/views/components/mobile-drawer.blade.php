@props(['navItems'])

<template x-teleport="body">
    <div x-show="sidebarOpen" class="z-drawer lg:hidden" style="display: none;" x-cloak>
        <div
            class="fixed inset-0 bg-overlay"
            x-show="sidebarOpen"
            x-transition.opacity.duration.150ms
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        <div
            x-show="sidebarOpen"
            x-transition:enter="transition ease-standard duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-standard duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            x-trap.noscroll.inert="sidebarOpen"
            @keydown.escape.window="sidebarOpen = false"
            role="dialog"
            aria-modal="true"
            aria-label="Menu de navegação"
            class="app-sidebar fixed inset-y-0 left-0 flex w-[18.5rem] max-w-[82%] flex-col gap-4 p-4 pt-[max(1rem,env(safe-area-inset-top))] pb-[max(1rem,env(safe-area-inset-bottom))]"
        >
            <div class="flex items-center justify-between gap-3">
                <div class="sidebar-brand flex-1">
                    <div class="min-w-0">
                        <img src="{{ asset('images/bakoftec-logo.png') }}" alt="Bakof Tec" class="sidebar-logo">
                        <p>Suíte RH · Avaliações</p>
                    </div>
                </div>
                <button type="button" @click="sidebarOpen = false" class="sidebar-icon-button" aria-label="Fechar menu">
                    <i data-lucide="x" class="size-4" aria-hidden="true"></i>
                </button>
            </div>

            <nav class="flex flex-1 flex-col gap-1 overflow-y-auto py-1" aria-label="Navegação principal">
                @foreach ($navItems as [$label, $routeName, $icon])
                    <a
                        href="{{ route($routeName) }}"
                        title="{{ $label }}"
                        @if (request()->routeIs($routeName)) aria-current="page" @endif
                        @if ($closeOnClick ?? true) @click="sidebarOpen = false" @endif
                        class="sidebar-nav-item {{ request()->routeIs($routeName) ? 'is-active' : '' }}"
                    >
                        <i data-lucide="{{ $icon }}" class="size-4 shrink-0" aria-hidden="true"></i>
                        <span class="truncate">{{ $label }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="flex flex-col gap-3 border-t border-white/10 pt-4">
                <a href="{{ route('perfil.edit') }}" class="sidebar-user-card" @click="sidebarOpen = false">
                    <div class="min-w-0">
                        <p class="truncate text-xs uppercase tracking-wide text-white/50">{{ auth()->user()->role->value }}</p>
                        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    </div>
                    <i data-lucide="user-cog" class="size-4 shrink-0 text-white/55" aria-hidden="true"></i>
                </a>

                <div class="grid grid-cols-2 gap-2">
                    <x-install-app-button class="sidebar-mobile-action" />
                    <x-theme-toggle class="sidebar-mobile-action" />
                </div>

                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-nav-item w-full">
                        <i data-lucide="log-out" class="size-4" aria-hidden="true"></i>
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
