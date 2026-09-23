@extends('layouts.app')

@section('title', 'Ranking')

@section('content')
    <h1>Ranking</h1>
    <p class="muted">Los 20 tags con más grafitis en la ciudad.</p>

    @forelse ($tags as $i => $t)
        <a class="ranking-row" href="{{ route('tags.show', $t) }}">
            <span class="pos">#{{ $i + 1 }}</span>
            @if ($img = $t->imageUrl())
                <img src="{{ $img }}" alt="" loading="lazy">
            @endif
            <span class="name">{{ $t->text }}</span>
            <span class="count">{{ $t->graffitis_count }}</span>
        </a>
    @empty
        <p class="muted">Todavía no hay grafitis registrados. ¡Registra el primero!</p>
    @endforelse
@endsection
