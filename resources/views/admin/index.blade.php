@extends('layouts.app')

@section('title', 'Administrar')

@section('content')
    <h1>Administrar</h1>
    @include('admin.nav')

    <div class="stats">
        <div><strong>{{ $counts['users'] }}</strong><span>usuarios</span></div>
        <div><strong>{{ $counts['tags'] }}</strong><span>tags</span></div>
        <div><strong>{{ $counts['graffitis'] }}</strong><span>grafitis</span></div>
        <div><strong>{{ $counts['photos'] }}</strong><span>fotos</span></div>
    </div>

    <a class="big-button {{ $adminUnreadReplies ? 'accent' : '' }}" href="{{ route('admin.inbox') }}">
        <strong>Mensajes</strong>
        <span>Escríbele a un usuario o a todos · {{ $adminUnreadReplies === 1 ? '1 respuesta sin leer' : "{$adminUnreadReplies} respuestas sin leer" }}</span>
    </a>

    <a class="big-button {{ $unread ? 'accent' : '' }}" href="{{ route('admin.messages') }}">
        <strong>Contacto</strong>
        <span>{{ $unread === 1 ? '1 sin leer' : "{$unread} sin leer" }}</span>
    </a>

    <p class="muted small">
        Para borrar una foto, entra al perfil del tag o a la foto: con tu cuenta de administrador verás
        un botón «Borrar». Los curadores (se nombran en Usuarios) también ven ese botón, pero no
        entran a este panel.
    </p>
@endsection
