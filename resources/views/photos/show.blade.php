@extends('layouts.app')

@section('title', $tag->text)

@section('content')
    @if ($authorBlocked)
        <div class="blocked-note">
            <p>Bloqueaste a quien subió esta foto, así que no la ves.</p>
            <form method="POST" action="{{ route('users.block', $photo->user_id) }}">
                @csrf
                <button class="button secondary small-button">Desbloquear</button>
            </form>
        </div>
    @else
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
    @endif

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

    @auth
        <div class="photo-safety muted small">
            @include('partials.report-form', ['action' => route('photos.report', $photo)])
            @if ($photo->user_id && $photo->user_id !== auth()->id() && ! $authorBlocked)
                <form method="POST" action="{{ route('users.block', $photo->user_id) }}" class="inline"
                      data-confirm="¿Bloquear a quien subió esta foto? Dejarás de ver sus fotos y comentarios.">
                    @csrf
                    <button class="link-button">Bloquear a quien la subió</button>
                </form>
            @endif
        </div>
    @else
        <p class="photo-safety muted small"><a href="{{ route('login') }}">Entra</a> para reportar esta foto.</p>
    @endauth

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
                    @if ($comment->user_id !== auth()->id())
                        @include('partials.report-form', ['action' => route('comments.report', $comment)])
                        <form method="POST" action="{{ route('users.block', $comment->user_id) }}" class="inline"
                              data-confirm="¿Bloquear a {{ $comment->user->username }}? Dejarás de ver sus fotos y comentarios.">
                            @csrf
                            <button class="link-button">Bloquear</button>
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
