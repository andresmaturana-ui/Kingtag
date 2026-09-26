@extends('layouts.app')

@section('title', 'Mis mensajes')

@section('content')
    <h1>Mis mensajes</h1>

    @include('partials.errors')

    <details class="inbox-write" @if ($errors->any()) open @endif>
        <summary class="button secondary">Escribirle a TAGKING</summary>
        <form method="POST" action="{{ route('inbox.reply') }}" class="form">
            @csrf
            <textarea name="body" rows="4" maxlength="2000" required aria-label="Tu mensaje" placeholder="Tu mensaje">{{ old('body') }}</textarea>
            <button type="submit" class="button">Enviar</button>
        </form>
    </details>

    <div class="inbox">
        @forelse ($items as $item)
            @if ($item instanceof \App\Models\VoteNotice)
                @php($tag = $item->photo->graffiti->tag)
                <a href="{{ route('photos.show', $item->photo) }}" @class(['inbox-item', 'notice', $item->kind, 'new' => ! $item->read_at])>
                    <span class="notice-icon">@include('partials.vote-icon', ['vote' => 'king'])</span>
                    <span class="notice-text">
                        <strong>{{ $item->actor->username }}</strong>
                        le dio <b>King</b>
                        {{ $item->photo->user_id === auth()->id() ? 'a tu foto de' : 'a una foto de tu tag' }}
                        <span class="tag-name">{{ $tag->text }}</span>
                        <span class="muted small">{{ $item->created_at->format('d-m-Y H:i') }}</span>
                    </span>
                    <img src="{{ $item->photo->thumbUrl() }}" alt="" class="notice-thumb" loading="lazy">
                </a>
            @else
                <article @class(['inbox-item', 'message', 'mine' => ! $item->from_admin, 'new' => $item->from_admin && ! $item->read_at])>
                    <p class="muted small">
                        <strong>{{ $item->from_admin ? config('kingtag.name') : 'Tú' }}</strong>
                        @if ($item->broadcast_id) · para todos @endif
                        · {{ $item->created_at->format('d-m-Y H:i') }}
                    </p>
                    <p class="message-body">{{ $item->body }}</p>
                </article>
            @endif
        @empty
            <p class="muted">Aún no tienes mensajes. Aquí verás lo que te escriba {{ config('kingtag.name') }} y cuando alguien le dé King a tus fotos o a tu tag.</p>
        @endforelse
    </div>
@endsection
