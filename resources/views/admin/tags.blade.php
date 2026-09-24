@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    <h1>Tags</h1>
    @include('admin.nav')

    <form method="GET" class="search-form">
        <input name="q" value="{{ $q }}" placeholder="Buscar tag" aria-label="Buscar tag">
        <button class="button">Buscar</button>
    </form>

    @forelse ($tags as $t)
        <article class="admin-item">
            <a href="{{ route('tags.show', $t) }}" class="admin-title">{{ $t->text }}</a>
            <p class="muted small">
                {{ $t->artist ? 'Artista: '.$t->artist->username : 'Sin reclamar' }} ·
                {{ $t->graffitis_count }} {{ $t->graffitis_count === 1 ? 'grafiti' : 'grafitis' }} ·
                {{ $t->photos_count }} {{ $t->photos_count === 1 ? 'foto' : 'fotos' }}
            </p>
            <div class="admin-actions">
                <a class="button secondary small-button" href="{{ route('tags.show', $t) }}">Ver fotos</a>
                @if ($t->artist)
                    <form method="POST" action="{{ route('admin.tags.unclaim', $t) }}" data-confirm="¿Quitarle el tag {{ $t->text }} a {{ $t->artist->username }}? Sus grafitis se mantienen.">
                        @csrf
                        <button class="button secondary small-button">Quitar dueño</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.tags.delete', $t) }}" data-confirm="¿Borrar el tag {{ $t->text }} con sus {{ $t->graffitis_count }} grafitis y {{ $t->photos_count }} fotos? No se puede deshacer.">
                    @csrf
                    @method('DELETE')
                    <button class="button danger small-button">Borrar tag</button>
                </form>
            </div>
        </article>
    @empty
        <p class="muted">No hay tags.</p>
    @endforelse

    {{ $tags->links('admin.pagination') }}
@endsection
