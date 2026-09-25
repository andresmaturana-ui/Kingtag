@extends('layouts.app')

@section('title', 'Buscar Tags')

@include('partials.leaflet')

@section('content')
    <h1>Buscar Tags</h1>

    <form method="GET" action="{{ route('search') }}" class="search-form">
        <input name="q" value="{{ $q }}" placeholder="¿Qué dice el tag?" autocapitalize="characters" aria-label="Buscar por texto">
        <button type="submit" class="button">Buscar</button>
    </form>

    @if ($q !== '')
        <h2>Resultados para “{{ $q }}”</h2>
        @forelse ($results as $t)
            <a class="ranking-row" href="{{ route('tags.show', $t) }}">
                @if ($img = $t->imageUrl())
                    <img src="{{ $img }}" alt="" loading="lazy">
                @endif
                <span class="name">{{ $t->text }}</span>
                <span class="count">{{ $t->graffitis_count }}</span>
            </a>
        @empty
            <p class="muted">No encontramos tags con ese texto.</p>
        @endforelse
    @endif

    <h2>Cerca de ti</h2>
    <p class="muted small" data-nearby-status>Buscando tu ubicación…</p>
    {{-- Se ven 3 a la vez; si hay más, se deslizan hacia el lado --}}
    <div class="feed carousel" data-nearby></div>

    <h2>Mapa</h2>
    <div class="map tall" data-map data-live></div>
@endsection
