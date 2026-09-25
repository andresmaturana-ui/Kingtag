@extends('layouts.app')

@section('title', 'Ayuda')

{{--
    El PDF descargable (public/manual/manual-tagking.pdf) es esta misma página
    impresa. Si cambias el texto, vuelve a generarlo con la app corriendo:
    chromium --headless --no-pdf-header-footer --print-to-pdf=public/manual/manual-tagking.pdf http://localhost:8000/ayuda
--}}

@section('content')
    <section class="manual-cover">
        <img src="/img/logo.svg" alt="{{ config('kingtag.name') }}" width="258" height="108">
        <p class="manual-cover-title">Manual de uso</p>
        <p class="muted">Los tags de la ciudad, en un mapa.</p>
        <p class="muted small">tagking.cl</p>
    </section>

    <h1>Cómo usar {{ config('kingtag.name') }}</h1>
    <p class="muted">{{ config('kingtag.name') }} guarda los tags de la ciudad en un mapa. Tú fotografías un tag en la calle, la app anota dónde está, y cada tag suma puntos en el ranking por cada muralla distinta donde aparece.</p>

    <p class="manual-download"><a class="button" href="/manual/manual-tagking.pdf" download>Descargar manual en PDF</a></p>

    <nav class="manual-index" aria-label="Contenido">
        <a href="#cuenta"><b>1</b> Crea tu cuenta</a>
        <a href="#registrar"><b>2</b> Registra un tag</a>
        <a href="#tu-tag"><b>3</b> Ingresa tu tag</a>
        <a href="#buscar"><b>4</b> Busca tags</a>
        <a href="#ranking"><b>5</b> El ranking</a>
        <a href="#fotos"><b>6</b> Fotos, me gusta y comentarios</a>
        <a href="#instalar"><b>7</b> Instala la app en tu teléfono</a>
        <a href="#preguntas"><b>8</b> Preguntas frecuentes</a>
    </nav>

    <section class="manual-step" id="cuenta">
        <h2><b>1</b> Crea tu cuenta</h2>
        <p>Para ver el mapa, el ranking y las fotos no necesitas cuenta. Para registrar tags, dar me gusta o comentar, sí.</p>
        <ol>
            <li>Abre el menú <span class="manual-key">☰</span> arriba a la derecha y toca <strong>Crear cuenta</strong>.</li>
            <li>Elige un <strong>usuario</strong> (de 3 a 30 letras) y una <strong>clave</strong> de al menos 6 caracteres.</li>
            <li>Toca <strong>Crear cuenta</strong>. Listo, ya entraste.</li>
        </ol>
        <p class="manual-tip">No pedimos correo. Anota tu clave en un lugar seguro: si la olvidas, escríbenos desde <strong>Contacto</strong> y te damos una nueva.</p>
    </section>

    <section class="manual-step" id="registrar">
        <h2><b>2</b> Registra un tag</h2>
        <p>Viste un tag en una muralla. Así lo subes al mapa:</p>
        <ol>
            <li>En el inicio toca el botón amarillo <span class="manual-key accent">Registrar tag</span>.</li>
            <li>Toca <strong>Sacar foto</strong>: se abre la cámara. Fotografía el tag completo.</li>
            <li>Escribe <strong>qué dice el tag</strong>, tal como se lee.</li>
            <li>Espera a que la app encuentre tu ubicación. Si te pregunta, dale permiso para usar tu ubicación.</li>
            <li>Toca <strong>Registrar</strong>.</li>
        </ol>
        <p class="manual-tip">Si ese mismo tag ya estaba registrado a menos de 20 metros, es el mismo grafiti: tu foto se suma a ese grafiti y no se cuenta dos veces.</p>
    </section>

    <section class="manual-step" id="tu-tag">
        <h2><b>3</b> Ingresa tu tag</h2>
        <p>Si eres artista, reclama tu tag para que todos sus grafitis aparezcan en tu perfil.</p>
        <ol>
            <li>En el inicio toca <span class="manual-key">Ingresa tu tag</span>.</li>
            <li>Escribe tu tag y agrega una foto para tu perfil. Puedes sacarla en el momento o elegirla de la galería.</li>
            <li>Toca <strong>Reclamar mi tag</strong>.</li>
        </ol>
        <p>Cada cuenta puede tener un solo tag, y cada tag un solo artista. Si ya reclamaste tu tag, en esa misma pantalla puedes cambiar su foto. Tu perfil está en el menú, en <strong>Mi perfil</strong>.</p>
        <p class="manual-tip">¿Alguien reclamó tu tag antes que tú? Escríbenos desde <strong>Contacto</strong> y lo revisamos.</p>
    </section>

    <section class="manual-step" id="buscar">
        <h2><b>4</b> Busca tags</h2>
        <p>Toca <span class="manual-key">Buscar tag</span> en el inicio, o <strong>Buscar</strong> en el menú. Hay tres formas de buscar:</p>
        <ul>
            <li><strong>Por texto:</strong> escribe parte del tag y toca Buscar. Verás los tags que coinciden y cuántos grafitis tiene cada uno.</li>
            <li><strong>En el mapa:</strong> cada foto redonda es un grafiti. Mueve el mapa y toca una foto para ver el tag.</li>
            <li><strong>Cerca de ti:</strong> abajo aparecen los grafitis a menos de 100 metros de donde estás, del más cercano al más lejano.</li>
        </ul>
    </section>

    <section class="manual-step" id="ranking">
        <h2><b>5</b> El ranking</h2>
        <p>En el menú, <strong>Ranking</strong> muestra los 20 tags con más grafitis de la ciudad. Cada muralla distinta cuenta como un grafiti; varias fotos del mismo grafiti no suman más.</p>
        <p>En la página de cada tag ves su posición, el tag que va justo arriba y el que va justo abajo, un mapa con todos sus grafitis y sus fotos.</p>
    </section>

    <section class="manual-step" id="fotos">
        <h2><b>6</b> Fotos, me gusta y comentarios</h2>
        <p>En el inicio, <strong>Lo último</strong> muestra las fotos más nuevas. Baja con el dedo y se van cargando más.</p>
        <ul>
            <li>Toca una foto para verla en grande.</li>
            <li>Toca <strong>♡ Me gusta</strong> para darle tu me gusta. Tócalo de nuevo para quitarlo.</li>
            <li>Escribe abajo y toca <strong>Comentar</strong>. Puedes borrar tus propios comentarios.</li>
            <li>Toca el nombre del tag para ver todo lo de ese tag.</li>
        </ul>
    </section>

    <section class="manual-step" id="instalar">
        <h2><b>7</b> Instala la app en tu teléfono</h2>
        <p>{{ config('kingtag.name') }} funciona desde el navegador, sin descargar nada de una tienda. Para tenerla como una app más, con su ícono:</p>
        <ul>
            <li><strong>Android (Chrome):</strong> abre tagking.cl, toca los tres puntos <span class="manual-key">⋮</span> arriba a la derecha y elige <strong>Agregar a la pantalla principal</strong> o <strong>Instalar app</strong>.</li>
            <li><strong>iPhone (Safari):</strong> abre tagking.cl, toca el botón compartir <span class="manual-key">⬆</span> abajo y elige <strong>Agregar a inicio</strong>.</li>
        </ul>
    </section>

    <section class="manual-step" id="preguntas">
        <h2><b>8</b> Preguntas frecuentes</h2>
        <dl class="manual-faq">
            <dt>La app no encuentra mi ubicación.</dt>
            <dd>Activa el GPS del teléfono y dale permiso de ubicación al navegador. Si lo rechazaste antes, cámbialo en los ajustes del navegador para tagking.cl.</dd>

            <dt>Olvidé mi clave.</dt>
            <dd>Escríbenos desde <strong>Contacto</strong> con tu usuario y te damos una clave nueva.</dd>

            <dt>Registré un tag con el texto mal escrito.</dt>
            <dd>Escríbenos desde <strong>Contacto</strong> y lo revisamos.</dd>

            <dt>Hay una foto que no debería estar.</dt>
            <dd>Avísanos desde <strong>Contacto</strong> y la revisamos.</dd>

            <dt>¿Por qué mi tag no sube en el ranking?</dt>
            <dd>El ranking cuenta murallas distintas. Registrar otra foto del mismo grafiti no suma; registrar el tag en otro lugar sí.</dd>
        </dl>
        <p class="manual-download"><a class="button secondary" href="{{ route('contact') }}">Escríbenos</a></p>
    </section>
@endsection
