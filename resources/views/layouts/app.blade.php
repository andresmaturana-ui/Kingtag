<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#111111">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') · @endif{{ config('kingtag.name') }}</title>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('kingtag.name') }}">
    <meta property="og:title" content="@hasSection('title')@yield('title')@else{{ config('kingtag.name') }}@endif">
    <meta property="og:description" content="Los tags de la ciudad, en un mapa.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('img/og-image.jpg') }}?v={{ filemtime(public_path('img/og-image.jpg')) }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icons/icon-192.png?v={{ filemtime(public_path('icons/icon-192.png')) }}">
    <link rel="apple-touch-icon" href="/icons/icon-192.png?v={{ filemtime(public_path('icons/icon-192.png')) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    @stack('head')
    <link rel="preload" href="/fonts/rock-salt.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/css/app.css?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <header class="topbar">
        <a href="{{ route('home') }}" class="logo"><img src="/img/logo.svg?v={{ filemtime(public_path('img/logo.svg')) }}" alt="{{ config('kingtag.name') }}" width="202" height="71"></a>
        <button class="burger" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu" data-burger>
            <span></span><span></span><span></span>
        </button>
    </header>

    <nav id="menu" class="menu" hidden>
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('search') }}">Buscar</a>
        <a href="{{ route('ranking') }}">Ranking</a>
        <a href="{{ route('help') }}">Ayuda</a>
        <a href="{{ route('contact') }}">Contacto</a>
        @auth
            <a href="{{ route('profile') }}">Mi perfil</a>
            @if (auth()->user()->is_admin)
                <a href="{{ route('admin.index') }}">Administrar</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Salir ({{ auth()->user()->username }})</button>
            </form>
        @else
            <a href="{{ route('login') }}">Entrar</a>
            <a href="{{ route('register') }}">Crear cuenta</a>
        @endauth
    </nav>

    <main class="page">
        @if (session('status'))
            <p class="flash">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
    <script src="/js/app.js?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
