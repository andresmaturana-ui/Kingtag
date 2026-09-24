@extends('layouts.app')

@section('content')
    <section class="home">
        <p class="tagline">Los tags de la ciudad, en un mapa.</p>

        <nav class="home-buttons">
            <a class="home-button" href="{{ route('my-tag.create') }}">Ingresa tu tag</a>
            <a class="home-button accent" href="{{ route('sightings.create') }}">Registrar tag</a>
            <a class="home-button" href="{{ route('search') }}">Buscar tag</a>
        </nav>
    </section>

    <h2>Lo último</h2>
    @if ($photos->isNotEmpty())
        <div class="feed">
            @foreach ($photos as $photo)
                <a href="{{ route('photos.show', $photo) }}">
                    <img src="{{ $photo->thumbUrl() }}" alt="Grafiti de {{ $photo->graffiti->tag->text }}" loading="lazy">
                    <span>{{ $photo->graffiti->tag->text }}@if ($photo->likers_count) · ♥ {{ $photo->likers_count }}@endif</span>
                </a>
            @endforeach
        </div>

        @if ($photos->hasMorePages())
            <p class="more"><a class="button secondary" href="{{ $photos->nextPageUrl() }}">Ver más</a></p>
        @endif
    @else
        <p class="muted">Todavía no hay grafitis. ¡Registra el primero!</p>
    @endif
@endsection
