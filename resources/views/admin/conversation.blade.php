@extends('layouts.app')

@section('title', "Mensajes con {$user->username}")

@section('content')
    <h1>{{ $user->username }}</h1>
    @include('admin.nav')

    <div class="chat">
        @forelse ($messages as $m)
            <div @class(['chat-bubble', 'mine' => $m->from_admin])>
                <p class="message-body">{{ $m->body }}</p>
                <p class="muted small">
                    {{ $m->from_admin ? ($m->broadcast_id ? 'Aviso a todos' : 'TAGKING'.($m->sender ? ' ('.$m->sender->username.')' : '')) : $user->username }}
                    · {{ $m->created_at->format('d-m-Y H:i') }}
                    @if ($m->from_admin) · {{ $m->read_at ? 'leído' : 'no leído aún' }} @endif
                </p>
            </div>
        @empty
            <p class="muted">Todavía no se han escrito.</p>
        @endforelse
    </div>

    @include('partials.errors')

    <form method="POST" action="{{ route('admin.inbox.send', $user) }}" class="form">
        @csrf
        <textarea name="body" rows="4" maxlength="2000" required aria-label="Mensaje para {{ $user->username }}" placeholder="Mensaje para {{ $user->username }}">{{ old('body') }}</textarea>
        <button type="submit" class="button">Enviar a {{ $user->username }}</button>
    </form>
@endsection
