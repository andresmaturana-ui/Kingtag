@extends('layouts.app')

@section('title', 'Ayuda')

{{--
    El PDF descargable (public/manual/manual-tagking.pdf) es esta misma página
    impresa. Si cambias el texto, vuelve a generarlo con la app corriendo:
    chromium --headless --no-pdf-header-footer --print-to-pdf=public/manual/manual-tagking.pdf http://localhost:8000/ayuda
    Las capturas (public/img/manual) se sacaron con datos de ejemplo, no de usuarios reales.
--}}

@php($name = config('kingtag.name'))

@section('content')
    <section class="manual-cover">
        <img src="/img/logo.svg" alt="{{ $name }}" width="258" height="108">
        <p class="manual-cover-title">Manual de uso</p>
        <p class="muted">Los tags de la ciudad, en un mapa.</p>
        <div class="manual-qr">
            <img src="/img/qr-tagking.svg" alt="Código QR a tagking.cl" width="140" height="140">
            <p>Escanea para abrir la app<br><strong>tagking.cl</strong></p>
        </div>
    </section>

    <h1>Cómo usar {{ $name }}</h1>
    <p class="muted">Todo lo que necesitas saber para registrar tags, reclamar el tuyo y subir en el ranking.</p>

    <p class="manual-download"><a class="button" href="/manual/manual-tagking.pdf" download>Descargar manual en PDF</a></p>

    <nav class="manual-index" aria-label="Contenido">
        <a href="#dinamica"><b>1</b> Qué es {{ $name }}</a>
        <a href="#cuenta"><b>2</b> Crea tu cuenta</a>
        <a href="#inicio"><b>3</b> El inicio y el menú</a>
        <a href="#registrar"><b>4</b> Registra un tag</a>
        <a href="#tu-tag"><b>5</b> Ingresa tu tag</a>
        <a href="#pagina-tag"><b>6</b> La página de cada tag</a>
        <a href="#buscar"><b>7</b> Busca tags</a>
        <a href="#ranking"><b>8</b> El ranking</a>
        <a href="#fotos"><b>9</b> Me gusta y comentarios</a>
        <a href="#instalar"><b>10</b> Instala la app</a>
        <a href="#preguntas"><b>11</b> Preguntas frecuentes</a>
    </nav>

    <section class="manual-step manual-intro" id="dinamica">
        <h2><b>1</b> Qué es {{ $name }}</h2>
        <p>{{ $name }} es el mapa de los tags de la ciudad. Entre todos fotografiamos los tags que vemos en las murallas, la app guarda dónde está cada uno, y así se arma un ranking de quién tiene su firma en más lugares.</p>

        <h3>Tres palabras que hay que conocer</h3>
        <div class="manual-flow">
            <div><strong>Tag</strong><span>La firma de un artista. Por ejemplo, KAOS.</span></div>
            <i aria-hidden="true">→</i>
            <div><strong>Grafiti</strong><span>Cada muralla donde aparece ese tag. KAOS en 4 murallas son 4 grafitis.</span></div>
            <i aria-hidden="true">→</i>
            <div><strong>Foto</strong><span>Cada vez que alguien registra un grafiti. Un mismo grafiti puede tener muchas fotos.</span></div>
        </div>

        <h3>Dos formas de jugar</h3>
        <div class="manual-roles">
            <div>
                <strong>Si recorres la ciudad</strong>
                <p>Eres cazador de tags. Cada vez que ves un tag en la calle, sácale una foto con la app. Tu foto lo pone en el mapa y le suma un grafiti a ese tag si esa muralla todavía no estaba registrada.</p>
            </div>
            <div>
                <strong>Si pintas</strong>
                <p>Reclama tu tag. Desde ese momento, todos los grafitis que la gente registre con tu firma aparecen en tu perfil, con tu nombre de artista y tu foto.</p>
            </div>
        </div>

        <h3>Cómo se gana</h3>
        <p>El ranking cuenta <strong>murallas distintas</strong>, no fotos. Sacarle diez fotos al mismo grafiti no suma más: lo que hace subir a un tag es aparecer en más lugares de la ciudad. Si dos fotos del mismo tag se registran a menos de 20 metros, la app entiende que es el mismo grafiti y las junta.</p>
    </section>

    <section class="manual-step has-shot" id="cuenta">
        <div class="manual-text">
            <h2><b>2</b> Crea tu cuenta</h2>
            <p>Mirar el mapa, el ranking y las fotos es libre. Para registrar tags, reclamar el tuyo, dar me gusta o comentar necesitas una cuenta.</p>
            <ol>
                <li>Abre el menú <span class="manual-key">☰</span> arriba a la derecha y toca <strong>Crear cuenta</strong>.</li>
                <li>Elige un <strong>usuario</strong> de 3 a 30 letras.</li>
                <li>Escribe una <strong>clave</strong> de al menos 6 caracteres, dos veces.</li>
                <li>Toca <strong>Crear cuenta</strong>. Listo, ya entraste.</li>
            </ol>
            <p class="manual-tip">No pedimos correo. Anota tu clave: si la olvidas, escríbenos desde <strong>Contacto</strong> y te damos una nueva.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/01-crear-cuenta.jpg" alt="Pantalla Crear cuenta" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot two" id="inicio">
        <div class="manual-text">
            <h2><b>3</b> El inicio y el menú</h2>
            <p>Al abrir {{ $name }} ves tres botones:</p>
            <ul>
                <li><span class="manual-key">Crear Tag</span> para reclamar tu firma si eres artista.</li>
                <li><span class="manual-key accent">Cazar Tag</span> para subir un tag que viste en la calle.</li>
                <li><span class="manual-key">Buscar Tags</span> para buscar por nombre o en el mapa.</li>
            </ul>
            <p>Abajo, <strong>Lo último</strong> muestra las fotos más nuevas. Baja con el dedo y se siguen cargando.</p>
            <p>El menú <span class="manual-key">☰</span> tiene todo lo demás: Buscar, Ranking, esta Ayuda, Contacto, tu perfil y Salir.</p>
        </div>
        <figure class="manual-shots">
            <img src="/img/manual/02-inicio.jpg" alt="Pantalla de inicio" loading="lazy" width="390" height="780">
            <img src="/img/manual/03-menu.jpg" alt="Menú abierto" loading="lazy" width="390" height="780">
        </figure>
    </section>

    <section class="manual-step has-shot" id="registrar">
        <div class="manual-text">
            <h2><b>4</b> Registra un tag</h2>
            <p>Viste un tag en una muralla. Párate frente a él y:</p>
            <ol>
                <li>En el inicio toca el botón amarillo <span class="manual-key accent">Cazar Tag</span>.</li>
                <li>Toca <strong>Sacar foto</strong>: se abre la cámara. Fotografía el tag completo.</li>
                <li>Escribe <strong>qué dice el tag</strong>, tal como se lee.</li>
                <li>Espera a que la app encuentre tu ubicación. Si te pregunta, dale permiso.</li>
                <li>Toca <strong>Registrar</strong>.</li>
            </ol>
            <p>La app te lleva a la página del tag y te dice qué pasó:</p>
            <ul>
                <li><strong>"Grafiti registrado"</strong>: era una muralla nueva, el tag suma un grafiti.</li>
                <li><strong>"Este grafiti ya estaba registrado"</strong>: alguien ya lo había subido a menos de 20 metros. Tu foto se suma a ese grafiti, pero no cuenta doble.</li>
            </ul>
            <p class="manual-tip">Registra el tag donde está: la ubicación sale de tu teléfono en ese momento.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/04-registrar.jpg" alt="Pantalla Registrar tag con una foto" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot" id="tu-tag">
        <div class="manual-text">
            <h2><b>5</b> Ingresa tu tag</h2>
            <p>Si pintas, reclama tu firma para que todos sus grafitis queden a tu nombre.</p>
            <ol>
                <li>En el inicio toca <span class="manual-key">Crear Tag</span>.</li>
                <li>Escribe tu tag.</li>
                <li>Agrega una foto para tu perfil: sácala en el momento o elígela de la galería.</li>
                <li>Toca <strong>Reclamar mi tag</strong>.</li>
            </ol>
            <p>Si otras personas ya habían registrado grafitis con tu tag, pasan a tu perfil al tiro. Cada cuenta tiene un solo tag y cada tag un solo artista. Después puedes volver aquí para cambiar la foto, y ver tu perfil en el menú, en <strong>Mi perfil</strong>.</p>
            <p class="manual-tip">¿Alguien reclamó tu tag antes que tú? Escríbenos desde <strong>Contacto</strong> y lo revisamos.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/05-ingresa-tu-tag.jpg" alt="Pantalla Ingresa tu tag" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot" id="pagina-tag">
        <div class="manual-text">
            <h2><b>6</b> La página de cada tag</h2>
            <p>Cada tag tiene su propia página. Llegas a ella tocando su nombre en cualquier parte de la app.</p>
            <ul>
                <li><strong>Arriba:</strong> la foto del tag, el artista que lo reclamó (o "Tag sin reclamar") y cuántos grafitis tiene.</li>
                <li><strong>Su lugar en el ranking:</strong> al centro, en amarillo, su posición. A los lados, el tag que va justo arriba y el que va justo abajo. Así sabes a quién tienes que pasar.</li>
                <li><strong>Dónde está:</strong> un mapa con un círculo por cada grafiti.</li>
                <li><strong>Fotos:</strong> todas las fotos que la gente ha registrado de ese tag.</li>
            </ul>
        </div>
        <figure class="manual-shots"><img src="/img/manual/06-tag.jpg" alt="Página del tag KAOS" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot two" id="buscar">
        <div class="manual-text">
            <h2><b>7</b> Busca tags</h2>
            <p>Toca <span class="manual-key">Buscar Tags</span> en el inicio o <strong>Buscar</strong> en el menú. Hay tres formas de buscar:</p>
            <ul>
                <li><strong>Por nombre:</strong> escribe parte del tag y toca Buscar. Verás los tags que coinciden y cuántos grafitis tiene cada uno.</li>
                <li><strong>En el mapa:</strong> cada círculo es un grafiti con su foto. Mueve el mapa, acerca con dos dedos y toca un círculo para ir a ese tag.</li>
                <li><strong>Cerca de ti:</strong> más abajo aparecen los grafitis a menos de 100 metros de donde estás, del más cercano al más lejano, con la distancia.</li>
            </ul>
        </div>
        <figure class="manual-shots">
            <img src="/img/manual/08-buscar.jpg" alt="Buscar por nombre y mapa" loading="lazy" width="390" height="780">
            <img src="/img/manual/09-cerca.jpg" alt="Grafitis cerca de ti" loading="lazy" width="390" height="780">
        </figure>
    </section>

    <section class="manual-step has-shot" id="ranking">
        <div class="manual-text">
            <h2><b>8</b> El ranking</h2>
            <p>En el menú, <strong>Ranking</strong> muestra los 20 tags con más grafitis de la ciudad. El número de la derecha es cuántas murallas distintas tiene cada uno.</p>
            <p>Para subir, el tag tiene que aparecer en más lugares. Varias fotos del mismo grafiti no suman.</p>
            <p>Toca cualquier tag para ver su página, su mapa y sus fotos.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/10-ranking.jpg" alt="Pantalla Ranking" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot" id="fotos">
        <div class="manual-text">
            <h2><b>9</b> Me gusta y comentarios</h2>
            <p>Toca cualquier foto para verla en grande.</p>
            <ul>
                <li>Toca <strong>♡ Me gusta</strong> para darle tu me gusta. Tócalo de nuevo para quitarlo. Las fotos con me gusta muestran un ♥ en el inicio.</li>
                <li>Escribe abajo y toca <strong>Comentar</strong>. Puedes borrar tus propios comentarios.</li>
                <li>Toca el nombre del tag para ir a su página.</li>
            </ul>
        </div>
        <figure class="manual-shots"><img src="/img/manual/11-foto.jpg" alt="Foto con me gusta y comentarios" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step" id="instalar">
        <h2><b>10</b> Instala la app en tu teléfono</h2>
        <p>{{ $name }} funciona desde el navegador, sin bajar nada de una tienda. Para tenerla como una app más, con su ícono en la pantalla:</p>
        <ul>
            <li><strong>Android (Chrome):</strong> abre tagking.cl, toca los tres puntos <span class="manual-key">⋮</span> arriba a la derecha y elige <strong>Agregar a la pantalla principal</strong> o <strong>Instalar app</strong>.</li>
            <li><strong>iPhone (Safari):</strong> abre tagking.cl, toca el botón compartir <span class="manual-key">⬆</span> abajo y elige <strong>Agregar a inicio</strong>.</li>
        </ul>
    </section>

    <section class="manual-step" id="preguntas">
        <h2><b>11</b> Preguntas frecuentes</h2>
        <dl class="manual-faq">
            <dt>La app no encuentra mi ubicación.</dt>
            <dd>Activa el GPS del teléfono y dale permiso de ubicación al navegador. Si lo rechazaste antes, cámbialo en los ajustes del navegador para tagking.cl.</dd>

            <dt>¿Por qué mi tag no sube en el ranking?</dt>
            <dd>El ranking cuenta murallas distintas. Otra foto del mismo grafiti no suma; el tag registrado en otro lugar sí.</dd>

            <dt>Olvidé mi clave.</dt>
            <dd>Escríbenos desde <strong>Contacto</strong> con tu usuario y te damos una clave nueva.</dd>

            <dt>Registré un tag con el texto mal escrito.</dt>
            <dd>Escríbenos desde <strong>Contacto</strong> y lo revisamos.</dd>

            <dt>Hay una foto que no debería estar.</dt>
            <dd>Avísanos desde <strong>Contacto</strong> y la revisamos.</dd>
        </dl>
        <p class="manual-download"><a class="button secondary" href="{{ route('contact') }}">Escríbenos</a></p>
    </section>

    <p class="manual-note muted small">Las pantallas de este manual usan tags y usuarios de ejemplo.</p>
@endsection
