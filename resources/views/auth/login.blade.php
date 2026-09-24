@extends('layouts.app')

@section('title', 'Entrar')

@section('content')
    <h1>Entrar</h1>

    @include('partials.errors')

    <form method="POST" action="{{ route('login') }}" class="form">
        @csrf
        <label>Usuario
            <input name="username" value="{{ old('username') }}" autocomplete="username" autocapitalize="none" required>
        </label>
        <label>Clave
            <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <button type="submit" class="button">Entrar</button>
    </form>

    <p class="muted">¿No tienes cuenta? <a href="{{ route('register') }}">Crear cuenta</a></p>
@endsection
