@extends('layouts.app')

@section('title', 'Borrar mi cuenta')

@section('content')
    <h1>Borrar mi cuenta</h1>

    <div class="prose">
        <p>Puedes borrar tu cuenta de {{ config('kingtag.name') }} cuando quieras. Al borrarla:</p>
        <ul>
            <li>Se borran tu usuario y tu clave.</li>
            <li>Se borran tus comentarios, tus King, tus mensajes y tu lista de bloqueados.</li>
            <li>Si reclamaste un tag, queda sin dueño y se borra su foto de perfil.</li>
            <li>Tus fotos de grafitis se borran si marcas la casilla. Si no, quedan publicadas sin tu nombre.</li>
        </ul>
        <p>Todo se borra en el momento y no se puede deshacer.</p>
    </div>

    @auth
        @include('partials.errors')

        <form method="POST" action="{{ route('account.destroy') }}" class="form danger-zone"
              data-confirm="¿Seguro? Tu cuenta se borra para siempre.">
            @csrf
            @method('DELETE')
            @if ($photoCount)
                <label class="check">
                    <input type="checkbox" name="delete_photos" value="1" @checked(old('delete_photos'))>
                    Borrar también {{ $photoCount === 1 ? 'mi foto' : "mis {$photoCount} fotos" }}
                </label>
            @endif
            <label>Escribe tu clave para confirmar
                <input type="password" name="password" autocomplete="current-password" required>
            </label>
            <button type="submit" class="button danger">Borrar mi cuenta para siempre</button>
        </form>
    @else
        <p><a class="button" href="{{ route('login') }}">Entrar para borrar mi cuenta</a></p>
        <p class="muted small">
            Si no recuerdas tu clave, escríbenos desde <a href="{{ route('contact') }}">Contacto</a> con tu nombre de usuario
            y borramos la cuenta por ti.
        </p>
    @endauth
@endsection
