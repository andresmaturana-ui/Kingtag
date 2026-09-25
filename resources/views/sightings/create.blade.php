@extends('layouts.app')

@section('title', 'Cazar Tag')

@section('content')
    <h1>Cazar Tag</h1>
    <p class="muted">Sácale una foto al tag en la muralla y escribe lo que dice. Guardamos dónde estás para ponerlo en el mapa.</p>

    @include('partials.errors')

    <form method="POST" action="{{ route('sightings.store') }}" enctype="multipart/form-data" class="form" data-photo-form data-geo-form>
        @csrf
        @include('partials.photo-input')

        @if ($readerEnabled)
            <p class="tag-reader muted small" hidden data-tag-reader data-url="{{ route('sightings.read') }}"></p>
        @endif

        <label>¿Qué dice el tag?
            <input name="text" value="{{ old('text') }}" required maxlength="60" autocapitalize="characters" data-tag-text>
        </label>

        <div class="tag-suggest" hidden data-tag-suggest data-url="{{ route('map.nearby') }}">
            <p class="muted small">¿Es alguno de estos? Están a menos de 100 m:</p>
            <div class="chips" data-tag-chips></div>
        </div>

        <input type="hidden" name="lat" value="{{ old('lat') }}" data-lat>
        <input type="hidden" name="lng" value="{{ old('lng') }}" data-lng>
        <p class="geo-status muted small" data-geo-status>Buscando tu ubicación…</p>

        <button type="submit" class="button" data-geo-submit>Registrar</button>
    </form>
@endsection
