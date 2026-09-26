@extends('layouts.app')

@section('title', $tag->text)

@section('content')
    <figure class="photo-full">
        <img src="{{ $photo->url() }}" alt="Grafiti de {{ $tag->text }}">
        <figcaption>
            <span>
                <a href="{{ route('tags.show', $tag) }}" class="photo-tag">{{ $tag->text }}</a>
                @if ($position)
                    <a href="{{ route('ranking') }}" class="photo-rank">#{{ $position }} en el ranking</a>
                @endif
            </span>
            <span class="muted small">{{ $photo->created_at->format('d-m-Y') }}</span>
        </figcaption>
    </figure>

    <div class="photo-actions">
        @include('partials.vote-buttons')
        @if ($canModerate)
            <form method="POST" action="{{ route('photos.delete', $photo) }}" data-confirm="¿Borrar esta foto? No se puede deshacer." class="photo-delete">
                @csrf
                @method('DELETE')
                <button class="button danger small-button">Borrar foto</button>
            </form>
        @endif
    </div>

    <h2 id="comentarios">Comentarios</h2>

    @forelse ($photo->comments as $comment)
        <div class="comment">
            <p><strong>{{ $comment->user->username }}</strong> {{ $comment->body }}</p>
            <div class="muted small">
                {{ $comment->created_at->format('d-m-Y H:i') }}
                @auth
                    @if ($comment->user_id === auth()->id() || auth()->user()->is_admin)
                        <form method="POST" action="{{ route('comments.delete', $comment) }}" data-confirm="¿Borrar este comentario?" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="link-button">Borrar</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>
    @empty
        <p class="muted">Todavía no hay comentarios.</p>
    @endforelse

    @auth
        @include('partials.errors')
        <form method="POST" action="{{ route('photos.comment', $photo) }}" class="form comment-form">
            @csrf
            <textarea name="body" rows="2" maxlength="500" placeholder="Escribe un comentario" aria-label="Comentario" required>{{ old('body') }}</textarea>
            <button class="button">Comentar</button>
        </form>
    @else
        <p class="muted"><a href="{{ route('login') }}">Entra</a> para comentar.</p>
    @endauth

    <p><a class="button secondary" href="{{ route('tags.show', $tag) }}">Ver todo de {{ $tag->text }}</a></p>
@endsection
