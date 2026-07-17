<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} - Avaliações RH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="error-page">
    <main class="error-shell">
        <section class="error-card">
            <img src="{{ asset('images/bakoftec-logo.png') }}" alt="Bakof Tec" class="error-logo">
            <p class="page-kicker">{{ $code }}</p>
            <h1>{{ $title }}</h1>
            <p>{{ $message }}</p>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn-primary">
                {{ auth()->check() ? 'Voltar ao dashboard' : 'Ir para login' }}
            </a>
        </section>
    </main>
</body>
</html>
