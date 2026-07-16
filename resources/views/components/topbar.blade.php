{{--
    Topbar — requer que o ancestral tenha x-data com `sidebarOpen`.
    O botão de menu só aparece abaixo de "lg" (onde a sidebar vira drawer).
--}}
<header class="app-topbar sticky top-0 z-sticky flex h-[var(--header-height)] items-center justify-between gap-3 px-4 pt-[env(safe-area-inset-top)] sm:px-6">
    <div class="flex min-w-0 items-center gap-3">
        <button
            type="button"
            x-ref="sidebarTrigger"
            @click="sidebarOpen = true"
            class="btn-ghost size-9 shrink-0 p-0 lg:hidden"
            aria-label="Abrir menu de navegação"
        >
            <i data-lucide="menu" class="size-5" aria-hidden="true"></i>
        </button>

        <div class="min-w-0">
            <p class="truncate text-xs uppercase tracking-wide text-foreground-subtle">{{ auth()->user()->role->value }}</p>
            <p class="max-w-[10rem] truncate text-sm font-semibold text-foreground sm:max-w-none">{{ auth()->user()->name }}</p>
        </div>
    </div>

    <div class="flex shrink-0 items-center gap-2">
        <span
            id="offline-indicator"
            hidden
            class="inline-flex items-center gap-1.5 rounded-full bg-warning-background px-2.5 py-1 text-xs font-medium text-warning"
        >
            <i data-lucide="wifi-off" class="size-3.5" aria-hidden="true"></i>
            <span class="hidden sm:inline">Offline</span>
        </span>

        <x-install-app-button class="hidden sm:block" />
        <x-theme-toggle class="hidden sm:block" />

        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-secondary">
                <i data-lucide="log-out" class="size-4" aria-hidden="true"></i>
                <span class="hidden sm:inline">Sair</span>
            </button>
        </form>
    </div>
</header>
