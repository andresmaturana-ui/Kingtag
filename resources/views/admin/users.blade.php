@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')
    <h1>Usuarios</h1>
    @include('admin.nav')

    <form method="GET" class="search-form">
        <input name="q" value="{{ $q }}" placeholder="Buscar usuario" aria-label="Buscar usuario" autocapitalize="none">
        <button class="button">Buscar</button>
    </form>

    @forelse ($users as $u)
        <article class="admin-item">
            <span class="admin-title">{{ $u->username }}@if ($u->is_admin) <span class="badge">admin</span>@endif @if ($u->is_curator) <span class="badge">curador</span>@endif</span>
            <p class="muted small">
                Desde {{ $u->created_at->format('d-m-Y') }} ·
                {{ $u->tag ? 'Tag: '.$u->tag->text : 'Sin tag' }} ·
                {{ $u->photos_count }} {{ $u->photos_count === 1 ? 'foto subida' : 'fotos subidas' }}
            </p>
            <div class="admin-actions">
                <a class="button small-button" href="{{ route('admin.inbox.show', $u) }}">Mensaje</a>
                <form method="POST" action="{{ route('admin.users.password', $u) }}" data-confirm="¿Crear una clave nueva para {{ $u->username }}? La actual dejará de funcionar.">
                    @csrf
                    <button class="button secondary small-button">Nueva clave</button>
                </form>
                <form method="POST" action="{{ route('admin.users.curator', $u) }}" data-confirm="{{ $u->is_curator ? "¿Quitarle el rol de curador a {$u->username}?" : "¿Hacer curador a {$u->username}? Podrá borrar fotos que no van con la línea editorial." }}">
                    @csrf
                    <button class="button secondary small-button">{{ $u->is_curator ? 'Quitar curador' : 'Hacer curador' }}</button>
                </form>
                @unless ($u->is(auth()->user()))
                    <form method="POST" action="{{ route('admin.users.delete', $u) }}" data-confirm="¿Borrar la cuenta {{ $u->username }}? Sus fotos se mantienen y su tag queda sin dueño.">
                        @csrf
                        @method('DELETE')
                        <button class="button danger small-button">Borrar cuenta</button>
                    </form>
                @endunless
            </div>
        </article>
    @empty
        <p class="muted">No hay usuarios.</p>
    @endforelse

    {{ $users->links('admin.pagination') }}
@endsection
