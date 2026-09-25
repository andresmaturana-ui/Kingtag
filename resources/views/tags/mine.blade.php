@extends('layouts.app')

@section('title', 'Going Up')

@section('content')
    <h1>Going Up</h1>

    @if ($tag)
        <p class="muted">Tu tag es <a href="{{ route('tags.show', $tag) }}"><strong>{{ $tag->text }}</strong></a>. Aquí puedes cambiar su foto.</p>
    @else
        <p class="muted">Si alguien ya registró grafitis con tu tag, al reclamarlo pasan a tu perfil.</p>
    @endif

    @include('partials.errors')

    <form method="POST" action="{{ route('my-tag.store') }}" enctype="multipart/form-data" class="form" data-photo-form>
        @csrf
        <label>¿Qué dice tu tag?
            <input name="text" value="{{ old('text', $tag?->text) }}" required maxlength="60" autocapitalize="characters" @readonly($tag)>
        </label>

        @include('partials.photo-input', ['allowGallery' => true])

        <button type="submit" class="button">{{ $tag ? 'Guardar foto' : 'Reclamar mi tag' }}</button>
    </form>
@endsection
