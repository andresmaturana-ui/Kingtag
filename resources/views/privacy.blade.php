@extends('layouts.app')

@section('title', 'Privacidad')

@php($name = config('kingtag.name'))

@section('content')
    <h1>Privacidad</h1>
    <p class="muted small">Política de privacidad y reglas de {{ $name }}. Última actualización: 29 de septiembre de 2026.</p>

    <div class="prose">
        <p>
            {{ $name }} es una app para registrar tags de grafiti en un mapa. La hace Andrés Maturana, en Chile.
            Aquí contamos qué datos guardamos, para qué y cómo puedes borrarlos.
            Para cualquier duda escríbenos desde <a href="{{ route('contact') }}">Contacto</a>.
        </p>

        <h2>Qué datos guardamos</h2>
        <ul>
            <li><strong>Tu cuenta:</strong> tu nombre de usuario y tu clave, guardada cifrada. No pedimos correo, teléfono ni nombre real.</li>
            <li><strong>Lo que publicas:</strong> las fotos de grafitis, el texto del tag, el lugar donde está el grafiti (la ubicación de tu teléfono al registrarlo) y la fecha. También tus comentarios y los King que das.</li>
            <li><strong>Tu tag:</strong> si reclamas un tag como tuyo, queda unido a tu cuenta junto con la foto de perfil que subas.</li>
            <li><strong>Mensajes:</strong> lo que nos escribes en Contacto o en Mis mensajes, y la forma de responderte si nos la das.</li>
            <li><strong>Datos técnicos:</strong> una cookie para mantener tu sesión abierta. El servidor anota por seguridad la dirección IP y el tipo de navegador de cada visita.</li>
        </ul>

        <h2>Tu ubicación</h2>
        <p>
            La app usa la ubicación de tu teléfono solo cuando registras un tag o cuando buscas grafitis cerca de ti,
            y siempre con tu permiso. Nunca sigue tu ubicación en segundo plano. Lo único que se guarda es el lugar
            del grafiti que registras, no dónde estás tú después.
        </p>

        <h2>Qué es público</h2>
        <p>
            Las fotos, el texto de los tags, el lugar de cada grafiti en el mapa, los King y los comentarios
            (con tu nombre de usuario) los puede ver cualquiera. Tu clave, tus mensajes y tu lista de bloqueados no.
        </p>

        <h2>Para qué los usamos</h2>
        <p>
            Solo para que la app funcione: mostrar el mapa, el ranking y las fotos, avisarte cuando alguien le da
            King a tus fotos, responder tus mensajes y cuidar que el contenido respete las reglas. No vendemos tus
            datos ni mostramos publicidad.
        </p>

        <h2>Con quién se comparten</h2>
        <ul>
            <li><strong>Hostinger</strong> guarda la app y sus datos en sus servidores.</li>
            <li><strong>OpenStreetMap</strong> entrega los mapas; al cargarlos, sus servidores ven tu dirección IP.</li>
            <li><strong>Anthropic</strong> (la IA Claude) recibe la foto que registras para leer qué dice el tag y sugerirlo. No guarda la foto para entrenar sus modelos.</li>
        </ul>

        <h2>Cuánto tiempo los guardamos</h2>
        <p>
            Mientras tengas tu cuenta. Los registros técnicos del servidor se borran solos después de un tiempo.
            Tu teléfono guarda una copia de las fotos que ya viste para que la app cargue más rápido.
        </p>

        <h2>Borrar tus datos</h2>
        <p>
            Puedes borrar tu cuenta cuando quieras desde <a href="{{ route('account.delete') }}">Borrar mi cuenta</a>
            (también está en Mi cuenta). Se borran tu usuario, tus comentarios, tus King y tus mensajes, y si quieres
            también tus fotos. Si no puedes entrar, escríbenos desde <a href="{{ route('contact') }}">Contacto</a> y
            lo hacemos por ti. También puedes pedirnos ver o corregir tus datos, como indica la ley chilena.
        </p>

        <h2>Edad</h2>
        <p>{{ $name }} es para personas de 13 años o más.</p>

        <h2 id="reglas">Reglas de la comunidad</h2>
        <p>Al usar {{ $name }} aceptas estas reglas. No se permite publicar:</p>
        <ul>
            <li>Contenido ofensivo, violento, sexual, racista o que discrimine.</li>
            <li>Acoso, amenazas o burlas contra otras personas.</li>
            <li>Fotos donde se vea la cara, la patente o los datos de alguien sin su permiso.</li>
            <li>Spam, publicidad o fotos que no sean de grafitis.</li>
        </ul>
        <p>
            Si ves algo así, tócale «Reportar» y lo revisamos. Si alguien te molesta, tócale «Bloquear» y dejarás de
            ver sus fotos y comentarios. Borramos el contenido que rompa las reglas y podemos borrar las cuentas que
            lo repitan.
        </p>

        <h2>Cambios</h2>
        <p>Si cambiamos esta política, lo avisaremos en la app y actualizaremos la fecha de arriba.</p>
    </div>
@endsection
