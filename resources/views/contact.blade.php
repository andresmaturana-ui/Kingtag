@extends('layouts.app')

@section('title', 'Contacto')

@section('content')
    <h1>Contacto</h1>
    <p class="muted">¿Una pregunta, un error o una foto que no debería estar? Escríbenos.</p>

    @include('partials.errors')

    <form method="POST" action="{{ route('contact.store') }}" class="form">
        @csrf
        @guest
            <label>Tu nombre (opcional)
                <input name="name" value="{{ old('name') }}" maxlength="80">
            </label>
        @endguest
        <label>Cómo te respondemos (opcional)
            <input name="contact" value="{{ old('contact') }}" maxlength="120" placeholder="Instagram, correo o teléfono">
        </label>
        <label>Mensaje
            <textarea name="message" rows="6" maxlength="2000" required>{{ old('message') }}</textarea>
        </label>
        <button type="submit" class="button">Enviar</button>
    </form>
@endsection
