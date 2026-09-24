@extends('layouts.app')

@section('title', 'Mensajes')

@section('content')
    <h1>Mensajes</h1>
    @include('admin.nav')

    @forelse ($messages as $m)
        <article @class(['admin-item', 'unread' => ! $m->read_at])>
            <p class="muted small">
                {{ $m->created_at->format('d-m-Y H:i') }} ·
                {{ $m->user?->username ?? ($m->name ?: 'Anónimo') }}
                @if ($m->contact)
                    · Responder a: {{ $m->contact }}
                @endif
            </p>
            <p class="message-body">{{ $m->message }}</p>
            <div class="admin-actions">
                <form method="POST" action="{{ route('admin.messages.read', $m) }}">
                    @csrf
                    <button class="button secondary small-button">{{ $m->read_at ? 'Marcar no leído' : 'Marcar leído' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.messages.delete', $m) }}" data-confirm="¿Borrar este mensaje?">
                    @csrf
                    @method('DELETE')
                    <button class="button danger small-button">Borrar</button>
                </form>
            </div>
        </article>
    @empty
        <p class="muted">No hay mensajes.</p>
    @endforelse

    {{ $messages->links('admin.pagination') }}
@endsection
