<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar - Avaliações RH</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="login-page"
    style="--login-bg-wide: url('{{ asset('images/login-bg-generated-wide.png') }}'); --login-bg-portrait: url('{{ asset('images/login-bg-generated-portrait.png') }}');"
>
    <main class="login-shell" aria-label="Entrar no sistema">
        <section class="login-panel">
            <div class="login-brand" aria-hidden="true">
                <img src="{{ asset('images/bakoftec-logo.png') }}" alt="" class="login-brand-logo">
                <p class="login-suite">Suíte RH · Avaliações</p>
            </div>

            <div class="login-copy">
                <p class="login-kicker">Acesso restrito</p>
                <h1>Entre no painel</h1>
                <p>Acompanhe avaliações, pendências e indicadores de efetivação em um único lugar.</p>
            </div>

            <form method="post" action="{{ route('login.store') }}" class="login-form">
                @csrf
                <label>
                    <span>E-mail</span>
                    <input name="email" type="email" value="{{ old('email') }}" autocomplete="email" class="app-input">
                </label>
                <label>
                    <span>Senha</span>
                    <input name="password" type="password" autocomplete="current-password" class="app-input">
                </label>
                @error('email')
                    <p class="login-error">{{ $message }}</p>
                @enderror
                <button class="btn-primary" type="submit">Entrar</button>
            </form>
        </section>
    </main>
</body>
</html>
