<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#f4f5f7">
    <meta name="application-name" content="Suíte RH - Avaliações">
    <meta name="description" content="Gestão de avaliações de desempenho, colaboradores e ciclos de RH.">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Avaliações RH">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">

    <title>{{ $title ?? 'Avaliações RH' }}</title>

    {{--
        Anti-flash de tema: aplica data-theme no <html> antes do primeiro
        paint, lendo a preferência salva (ou o sistema, se nunca escolhida).
        Mantido em sincronia com resources/js/theme.js.
    --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme') || 'light';
                var resolved = stored === 'system'
                    ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                    : stored;
                document.documentElement.setAttribute('data-theme', resolved);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen antialiased">
    @php
        $navItems = auth()->user()->isRh()
            ? [
                ['Dashboard', 'dashboard', 'layout-dashboard'],
                ['Colaboradores', 'colaboradores.index', 'users'],
                ['Gestores', 'gestores.index', 'user-check'],
                ['Usuários', 'usuarios.index', 'user-cog'],
                ['Setores', 'setores.index', 'building-2'],
                ['Unidades', 'unidades-negocio.index', 'building-2'],
                ['Formulários', 'formularios.index', 'list-checks'],
                ['Avaliações', 'avaliacoes.index', 'clipboard-check'],
                ['Relatórios', 'relatorios.index', 'bar-chart-3'],
                ['Configurações', 'configuracoes.index', 'settings'],
            ]
            : [
                ['Dashboard', 'dashboard', 'layout-dashboard'],
                ['Minhas avaliações', 'avaliacoes.index', 'clipboard-check'],
            ];
    @endphp

    <div x-data="{ sidebarOpen: false }" class="app-shell flex min-h-screen">
        <x-sidebar :nav-items="$navItems" />
        <x-mobile-drawer :nav-items="$navItems" />

        <main class="flex min-w-0 flex-1 flex-col">
            <x-topbar />

            <div class="app-content mx-auto w-full max-w-[var(--container-width)] flex-1 px-4 py-6 sm:px-6 sm:py-8">
                @if (session('status'))
                    <div class="mb-6 rounded-md border border-border bg-success-background px-4 py-3 text-sm text-success">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
