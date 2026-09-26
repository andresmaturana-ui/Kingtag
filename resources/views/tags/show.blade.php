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
            @include('partials.tag-votes', ['kings' => $kings, 'toys' => $toys])
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
                <div class="feed-item">
                    <a href="{{ route('photos.show', $photo) }}">
                        <img src="{{ $photo->thumbUrl() }}" alt="Grafiti de {{ $tag->text }}" loading="lazy">
                    </a>
                    @if ($canModerate)
                        <form method="POST" action="{{ route('photos.delete', $photo) }}" data-confirm="¿Borrar esta foto? No se puede deshacer.">
                            @csrf
                            @method('DELETE')
                            <button class="feed-delete">Borrar</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="muted">Todavía nadie ha registrado grafitis de este tag.</p>
    @endif

    @if ($isAdmin)
        <h2>Administrar</h2>
        <div class="admin-actions">
            @if ($tag->isClaimed())
                <form method="POST" action="{{ route('admin.tags.unclaim', $tag) }}" data-confirm="¿Quitarle este tag a {{ $tag->artist->username }}? Sus grafitis se mantienen.">
                    @csrf
                    <button class="button secondary small-button">Quitar dueño</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.tags.delete', $tag) }}" data-confirm="¿Borrar el tag {{ $tag->text }} con todos sus grafitis y fotos? No se puede deshacer.">
                @csrf
                @method('DELETE')
                <button class="button danger small-button">Borrar tag</button>
            </form>
        </div>
    @endif
@endsection
