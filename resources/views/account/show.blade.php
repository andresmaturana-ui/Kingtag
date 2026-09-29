@extends('layouts.app')

@section('title', 'Mi cuenta')

@section('content')
    <h1>Mi cuenta</h1>
    <p class="muted">Entraste como <strong>{{ auth()->user()->username }}</strong>.</p>

    <h2>Usuarios bloqueados</h2>
    @if ($blocked->isEmpty())
        <p class="muted">No has bloqueado a nadie. Para bloquear a alguien, toca «Bloquear» en una de sus fotos o comentarios.</p>
    @else
        <p class="muted small">No ves sus fotos ni sus comentarios.</p>
        <ul class="blocked-list">
            @foreach ($blocked as $user)
                <li>
                    <span>{{ $user->username }}</span>
                    <form method="POST" action="{{ route('users.block', $user) }}">
                        @csrf
                        <button class="button secondary small-button">Desbloquear</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    <h2>Tus datos</h2>
    <p><a href="{{ route('privacy') }}">Política de privacidad</a>: qué guardamos y para qué.</p>

    <section class="danger-zone">
        <h2>Borrar mi cuenta</h2>
        <p class="muted">Se borra tu cuenta con tus comentarios, tus King y tus mensajes. Puedes elegir borrar también tus fotos.</p>
        <a class="button danger" href="{{ route('account.delete') }}">Borrar mi cuenta</a>
    </section>
@endsection
