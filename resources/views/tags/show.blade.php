@extends('layouts.app')

@section('title', $tag->text)

@include('partials.leaflet')

@section('content')
    <section class="profile-head">
        @if ($image = $tag->imageUrl())
            <img src="{{ $image }}" alt="Tag {{ $tag->text }}" class="avatar">
        @endif
        <div>
            <h1>{{ $tag->text }}</h1>
            <p class="muted small">
                @if ($tag->isClaimed())
                    Artista: {{ $tag->artist->username }}
                @else
                    Tag sin reclamar
                @endif
                · {{ $graffitis->count() }} {{ $graffitis->count() === 1 ? 'grafiti' : 'grafitis' }}
            </p>
        </div>
    </section>

    @if ($rank['position'])
        <section class="rank-strip">
            @if ($rank['above'])
                @include('partials.rank-card', ['t' => $rank['above'], 'pos' => $rank['position'] - 1])
            @endif
            <div class="rank-card current">
                <span class="pos">#{{ $rank['position'] }}</span>
                <span>{{ $tag->text }}</span>
            </div>
            @if ($rank['below'])
                @include('partials.rank-card', ['t' => $rank['below'], 'pos' => $rank['position'] + 1])
            @endif
        </section>
    @endif

    @if ($graffitis->isNotEmpty())
        <h2>Dónde está</h2>
        <div class="map" data-map data-points="{{ $points->toJson() }}"></div>

        <h2>Fotos</h2>
        <div class="feed">
            @foreach ($photos as $photo)
                <a href="{{ $photo->url() }}" target="_blank" rel="noopener">
                    <img src="{{ $photo->thumbUrl() }}" alt="Grafiti de {{ $tag->text }}" loading="lazy">
                </a>
            @endforeach
        </div>
    @else
        <p class="muted">Todavía nadie ha registrado grafitis de este tag.</p>
    @endif
@endsection
