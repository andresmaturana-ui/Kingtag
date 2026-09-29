@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
    <h1>Reportes</h1>
    @include('admin.nav')

    <p class="muted small">Fotos y comentarios que los usuarios avisaron que no deberían estar. Si corresponde, bórralos; después márcalos como revisados.</p>

    @forelse ($reports as $r)
        <article @class(['admin-item', 'unread' => ! $r->resolved_at, 'resolved' => $r->resolved_at])>
            <p class="muted small">
                {{ $r->created_at->format('d-m-Y H:i') }} ·
                {{ $r->user?->username ?? 'Cuenta borrada' }} ·
                <strong>{{ $r->reasonLabel() }}</strong>
            </p>
            @if ($r->photo)
                <p>
                    <a href="{{ route('photos.show', $r->photo) }}"><img class="report-thumb" src="{{ $r->photo->thumbUrl() }}" alt="">Foto de {{ $r->photo->graffiti->tag->text }}</a>
                </p>
            @elseif ($r->comment)
                <p class="message-body"><strong>{{ $r->comment->user->username }}:</strong> {{ $r->comment->body }}</p>
                <p class="small"><a href="{{ route('photos.show', $r->comment->photo_id) }}#comentarios">Ver en la foto</a></p>
            @endif
            <div class="admin-actions">
                @if ($r->photo)
                    <form method="POST" action="{{ route('photos.delete', $r->photo) }}" data-confirm="¿Borrar esta foto? No se puede deshacer.">
                        @csrf
                        @method('DELETE')
                        <button class="button danger small-button">Borrar foto</button>
                    </form>
                @elseif ($r->comment)
                    <form method="POST" action="{{ route('comments.delete', $r->comment) }}" data-confirm="¿Borrar este comentario?">
                        @csrf
                        @method('DELETE')
                        <button class="button danger small-button">Borrar comentario</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.reports.resolve', $r) }}">
                    @csrf
                    <button class="button secondary small-button">{{ $r->resolved_at ? 'Marcar pendiente' : 'Marcar revisado' }}</button>
                </form>
            </div>
        </article>
    @empty
        <p class="muted">No hay reportes.</p>
    @endforelse

    {{ $reports->links('admin.pagination') }}
@endsection
