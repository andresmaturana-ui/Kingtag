@extends('layouts.app')

@section('content')
    <section class="home">
        <p class="tagline">Los tags de la ciudad, en un mapa.</p>

        <a class="big-button" href="{{ route('my-tag.create') }}">
            <strong>Ingresa tu tag</strong>
            <span>Eres artista: reclama tu tag y mira dónde está tu obra.</span>
        </a>

        <a class="big-button accent" href="{{ route('sightings.create') }}">
            <strong>Registrar tag</strong>
            <span>Viste un tag en la calle: sácale una foto.</span>
        </a>

        <a class="big-button" href="{{ route('search') }}">
            <strong>Buscar tag</strong>
            <span>En el mapa, por nombre o cerca de ti.</span>
        </a>
    </section>
@endsection
