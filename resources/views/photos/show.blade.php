@extends('layouts.app')

@section('title', $tag->text)

@section('content')
    <figure class="photo-full">
        <img src="{{ $photo->url() }}" alt="Grafiti de {{ $tag->text }}">
        <figcaption>
            <a href="{{ route('tags.show', $tag) }}" class="photo-tag">{{ $tag->text }}</a>
            <span class="muted small">{{ $photo->created_at->format('d-m-Y') }}</span>
        </figcaption>
    </figure>

    <div class="photo-actions">
        @auth
            <form method="POST" action="{{ route('photos.like', $photo) }}">
                @csrf
                <button @class(['like', 'liked' => $liked]) aria-pressed="{{ $liked ? 'true' : 'false' }}">
                    <span aria-hidden="true">{{ $liked ? '♥' : '♡' }}</span> Me gusta
                </button>
            </form>
        @else
            <a class="like" href="{{ route('login') }}"><span aria-hidden="true">♡</span> Me gusta</a>
        @endauth
        <span class="muted">{{ $photo->likers_count }} me gusta</span>
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
