@extends('layouts.app')

@section('content')
    <section class="home">
        <p class="tagline">Los tags de la ciudad, en un mapa.</p>

        <nav class="home-buttons">
            <a class="home-button" href="{{ route('my-tag.create') }}">Going Up</a>
            <a class="home-button accent" href="{{ route('sightings.create') }}">Spotting</a>
            <a class="home-button" href="{{ route('search') }}">Buscar Tags</a>
        </nav>
    </section>

    <h2>Lo último</h2>
    @if ($photos->isNotEmpty())
        <div class="feed" data-feed>
            @foreach ($photos as $photo)
                <div class="feed-card">
                    <a href="{{ route('photos.show', $photo) }}">
                        <img src="{{ $photo->thumbUrl() }}" alt="Grafiti de {{ $photo->graffiti->tag->text }}" loading="lazy">
                        <span>{{ $photo->graffiti->tag->text }}@isset($positions[$photo->graffiti->tag_id])<b class="rank-badge">#{{ $positions[$photo->graffiti->tag_id] }}</b>@endisset</span>
                    </a>
                    @include('partials.vote-buttons', ['compact' => true])
                </div>
            @endforeach
        </div>

        @if ($photos->hasMorePages())
            <p class="more" data-feed-more><a class="button secondary" href="{{ $photos->nextPageUrl() }}">Ver más</a></p>
        @endif
    @else
        <p class="muted">Todavía no hay grafitis. ¡Registra el primero!</p>
    @endif
@endsection
