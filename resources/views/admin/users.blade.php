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
            <span class="admin-title">{{ $u->username }}@if ($u->is_admin) <span class="badge">admin</span>@endif</span>
            <p class="muted small">
                Desde {{ $u->created_at->format('d-m-Y') }} ·
                {{ $u->tag ? 'Tag: '.$u->tag->text : 'Sin tag' }} ·
                {{ $u->photos_count }} {{ $u->photos_count === 1 ? 'foto subida' : 'fotos subidas' }}
            </p>
            <div class="admin-actions">
                <form method="POST" action="{{ route('admin.users.password', $u) }}" data-confirm="¿Crear una clave nueva para {{ $u->username }}? La actual dejará de funcionar.">
                    @csrf
                    <button class="button secondary small-button">Nueva clave</button>
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
