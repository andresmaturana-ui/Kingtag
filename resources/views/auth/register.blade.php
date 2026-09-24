@extends('layouts.app')

@section('title', 'Crear cuenta')

@section('content')
    <h1>Crear cuenta</h1>
    <p class="muted">Solo necesitas un usuario y una clave.</p>

    @include('partials.errors')

    <form method="POST" action="{{ route('register') }}" class="form">
        @csrf
        <label>Usuario
            <input name="username" value="{{ old('username') }}" autocomplete="username" autocapitalize="none" required minlength="3" maxlength="30">
        </label>
        <label>Clave
            <input type="password" name="password" autocomplete="new-password" required minlength="6">
        </label>
        <label>Repite la clave
            <input type="password" name="password_confirmation" autocomplete="new-password" required minlength="6">
        </label>
        <p class="muted small">Anota tu clave: como no pedimos correo, no hay forma automática de recuperarla.</p>
        <button type="submit" class="button">Crear cuenta</button>
    </form>

    <p class="muted">¿Ya tienes cuenta? <a href="{{ route('login') }}">Entrar</a></p>
@endsection
