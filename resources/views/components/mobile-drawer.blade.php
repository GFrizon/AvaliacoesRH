@props(['navItems'])

{{--
    Drawer de navegação mobile. Requer que o elemento ancestral tenha
    x-data com a propriedade `sidebarOpen` e o botão de abertura use
    `x-ref="sidebarTrigger"` (ver components/topbar.blade.php).
--}}
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
            class="app-sidebar fixed inset-y-0 left-0 flex w-72 max-w-[85%] flex-col gap-6 p-4 pt-[max(1rem,env(safe-area-inset-top))] pb-[max(1rem,env(safe-area-inset-bottom))]"
        >
            <div class="flex items-center justify-between gap-3 px-1">
                <div class="min-w-0">
                    <img src="{{ asset('images/bakoftec-logo.png') }}" alt="Bakof Tec" class="h-8 w-auto max-w-40 object-contain">
                    <p class="mt-1 truncate text-xs font-medium uppercase tracking-wide text-foreground-subtle">Suíte RH · Avaliações</p>
                </div>
                <button type="button" @click="sidebarOpen = false" class="btn-ghost size-9 shrink-0 p-0" aria-label="Fechar menu">
                    <i data-lucide="x" class="size-4" aria-hidden="true"></i>
                </button>
            </div>

            <nav class="flex flex-1 flex-col gap-1 overflow-y-auto" aria-label="Navegação principal">
                @include('partials.nav-links', ['navItems' => $navItems, 'closeOnClick' => true])
            </nav>

            <div class="flex flex-col gap-3 border-t border-border pt-4">
                <div class="flex items-center justify-between gap-2 px-1">
                    <div class="min-w-0">
                        <p class="truncate text-xs uppercase tracking-wide text-foreground-subtle">{{ auth()->user()->role->value }}</p>
                        <p class="truncate text-sm font-medium text-foreground">{{ auth()->user()->name }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-install-app-button />
                        <x-theme-toggle />
                    </div>
                </div>

                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary w-full">
                        <i data-lucide="log-out" class="size-4" aria-hidden="true"></i>
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
