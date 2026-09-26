@extends('layouts.app')

@section('title', 'Mensajes')

@section('content')
    <h1>Mensajes</h1>
    @include('admin.nav')

    @include('partials.errors')

    <h2>Aviso para todos</h2>
    <form method="POST" action="{{ route('admin.inbox.all') }}" class="form" data-confirm="¿Enviar este aviso a todos los usuarios?">
        @csrf
        <textarea name="body" rows="4" maxlength="2000" required aria-label="Aviso para todos" placeholder="Lo verán todos en «Mis mensajes»">{{ old('body') }}</textarea>
        <button type="submit" class="button">Enviar a todos</button>
    </form>
    <p class="muted small">Para escribirle a una sola persona, búscala en <a href="{{ route('admin.users') }}">Usuarios</a> y toca «Mensaje».</p>

    <h2>Conversaciones</h2>
    @forelse ($conversations as $m)
        @php($n = $unread[$m->user_id] ?? 0)
        <a href="{{ route('admin.inbox.show', $m->user_id) }}" @class(['admin-item', 'conversation-row', 'unread' => $n])>
            <span class="admin-title">{{ $m->user->username }} @if ($n)<span class="badge">{{ $n }} {{ $n === 1 ? 'nueva' : 'nuevas' }}</span>@endif</span>
            <span class="muted small">{{ $m->created_at->format('d-m-Y H:i') }} · {{ $m->from_admin ? 'Tú: ' : '' }}{{ \Illuminate\Support\Str::limit($m->body, 80) }}</span>
        </a>
    @empty
        <p class="muted">Todavía no hay conversaciones. Cuando le escribas a alguien o alguien te responda, aparecerá aquí.</p>
    @endforelse

    {{ $conversations->links('admin.pagination') }}

    @if ($broadcasts->isNotEmpty())
        <h2>Avisos enviados</h2>
        @foreach ($broadcasts as $b)
            <article class="admin-item">
                <p class="muted small">{{ $b->created_at->format('d-m-Y H:i') }} · leído por {{ $b->read_count }} de {{ $b->copies_count }}</p>
                <p class="message-body">{{ $b->body }}</p>
            </article>
        @endforeach
    @endif
@endsection
