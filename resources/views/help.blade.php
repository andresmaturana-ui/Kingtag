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
        <img src="/img/logo.svg?v={{ filemtime(public_path('img/logo.svg')) }}" alt="{{ $name }}" width="300" height="106">
        <p class="manual-cover-title">Manual de uso</p>
        <p class="muted">La calle habla, TAGKING anota.</p>
        <div class="manual-qr">
            <img src="/img/qr-tagking.svg" alt="Código QR a tagking.org" width="140" height="140">
            <p>Escanea para abrir la app<br><strong>tagking.org</strong></p>
        </div>
    </section>

    <h1>Cómo usar {{ $name }}</h1>
    <p class="muted">Todo lo que tenís que cachar pa' spottear tags, parar tu firma y subir en el ranking con tu crew.</p>

    <p class="manual-download"><a class="button" href="/manual/manual-tagking.pdf" download>Descargar manual en PDF</a></p>

    <nav class="manual-index" aria-label="Contenido">
        <a href="#dinamica"><b>1</b> Qué es {{ $name }}</a>
        <a href="#cuenta"><b>2</b> Crea tu cuenta</a>
        <a href="#inicio"><b>3</b> El inicio y el menú</a>
        <a href="#registrar"><b>4</b> Spotting</a>
        <a href="#tu-tag"><b>5</b> Going Up</a>
        <a href="#pagina-tag"><b>6</b> La página de cada tag</a>
        <a href="#buscar"><b>7</b> Buscar Tags</a>
        <a href="#ranking"><b>8</b> El ranking</a>
        <a href="#fotos"><b>9</b> King y comentarios</a>
        <a href="#mensajes"><b>10</b> Mis mensajes</a>
        <a href="#instalar"><b>11</b> Instala la app</a>
        <a href="#preguntas"><b>12</b> Preguntas frecuentes</a>
    </nav>

    <section class="manual-step manual-intro" id="dinamica">
        <h2><b>1</b> Qué es {{ $name }}</h2>
        <p>Wena. {{ $name }} es el mapa de los tags de la ciudad. Se acabó el cuento de "yo tengo más muros que tú": ahora queda todo anotado. Entre todos le sacamos foto a los tags que pillamos en la calle, la app guarda dónde está cada uno y así se arma el ranking de quién tiene su firma en más muros.</p>

        <h3>Tres palabras que tenís que cachar</h3>
        <div class="manual-flow">
            <div><strong>Tag</strong><span>La firma de un escritor. Por ejemplo, KAOS.</span></div>
            <i aria-hidden="true">→</i>
            <div><strong>Grafiti</strong><span>Cada muro donde está rayado ese tag. KAOS en 4 muros son 4 grafitis.</span></div>
            <i aria-hidden="true">→</i>
            <div><strong>Foto</strong><span>Cada vez que alguien spottea un grafiti. Un mismo muro puede tener caleta de fotos.</span></div>
        </div>

        <h3>Dos formas de moverse</h3>
        <div class="manual-roles">
            <div>
                <strong>Si andai en la calle</strong>
                <p>Erís spotter. Cada tag que pillai rayado, foto altiro con la app. Tu foto lo clava en el mapa y le suma un muro a ese tag si nadie lo había subido antes.</p>
            </div>
            <div>
                <strong>Si rayai</strong>
                <p>Para tu firma con Going Up. Desde ahí, todos los muros que la gente spottee con tu tag caen en tu perfil, a tu nombre y con tu foto. Que nadie te la robe.</p>
            </div>
        </div>

        <h3>Cómo se llega a King</h3>
        <p>Muro es muro. El ranking cuenta <strong>muros distintos</strong>, no fotos. Sacarle diez fotos al mismo grafiti no suma na': acá no se infla nada. Lo que te sube es estar en más lugares de la ciudad. Si dos fotos del mismo tag se suben a menos de 20 metros, la app cacha que es el mismo muro y las junta. Sal con tu crew, spotteen los tags de los cabros y suban juntos: ningún King llega solo.</p>
    </section>

    <section class="manual-step has-shot" id="cuenta">
        <div class="manual-text">
            <h2><b>2</b> Crea tu cuenta</h2>
            <p>Mirar el mapa, el ranking y las fotos es libre. Pa' spottear, parar tu firma, tirar King o comentar necesitai cuenta.</p>
            <ol>
                <li>Abre el menú <span class="manual-key">☰</span> arriba a la derecha y toca <strong>Crear cuenta</strong>.</li>
                <li>Elige un <strong>usuario</strong> de 3 a 30 letras.</li>
                <li>Escribe una <strong>clave</strong> de al menos 6 caracteres, dos veces.</li>
                <li>Toca <strong>Crear cuenta</strong>. Listo, estás adentro.</li>
            </ol>
            <p class="manual-tip">No pedimos correo. Anota tu clave: si se te olvida, escríbenos desde <strong>Contacto</strong> y te pasamos una nueva.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/01-crear-cuenta.jpg" alt="Pantalla Crear cuenta" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot two" id="inicio">
        <div class="manual-text">
            <h2><b>3</b> El inicio y el menú</h2>
            <p>Apenas abrís {{ $name }} tenís tres botones:</p>
            <ul>
                <li><span class="manual-key">Going Up</span> pa' parar tu firma si rayai.</li>
                <li><span class="manual-key accent">Spotting</span> pa' subir un tag que pillaste en la calle.</li>
                <li><span class="manual-key">Buscar Tags</span> pa' buscar por nombre o en el mapa.</li>
            </ul>
            <p>Abajo, en <strong>Lo último</strong>, sale lo más nuevo que se ha spotteado, cada foto con su botón <strong>👑 King</strong> y al lado, en amarillo, el puesto del tag en el ranking (por ejemplo <strong>#2</strong>). Toca el puesto y te vai al ranking. Baja con el dedo y siguen saliendo.</p>
            <p>En el menú <span class="manual-key">☰</span> está todo lo demás: Buscar, Ranking, esta Ayuda, Contacto, tu perfil, Mis mensajes y Salir. Si ves un puntito amarillo en el menú, te llegó algo.</p>
        </div>
        <figure class="manual-shots">
            <img src="/img/manual/02-inicio.jpg" alt="Pantalla de inicio" loading="lazy" width="390" height="780">
            <img src="/img/manual/03-menu.jpg" alt="Menú abierto" loading="lazy" width="390" height="780">
        </figure>
    </section>

    <section class="manual-step has-shot" id="registrar">
        <div class="manual-text">
            <h2><b>4</b> Spotting</h2>
            <p>Pillaste un tag rayado. Párate al frente y:</p>
            <ol>
                <li>En el inicio toca el botón amarillo <span class="manual-key accent">Spotting</span>.</li>
                <li>Toca <strong>Sacar foto</strong>: se abre la cámara. Sácale el tag entero, que se lea.</li>
                <li>Escribe <strong>qué dice el tag</strong>, tal cual. La app te hace la segunda de dos maneras:
                    <ul>
                        <li>La inteligencia artificial mira tu foto, te tira un <strong>"Parece que dice…"</strong> y lo escribe por ti. Revísalo, que a veces se le chispotea con los handstyles más enredados.</li>
                        <li>Abajo salen los tags que ya están subidos <strong>a menos de 100 metros</strong>. Si es uno de esos, tócalo y queda escrito igualito.</li>
                    </ul>
                </li>
                <li>Espera que la app te ubique. Si te pide permiso, dáselo. El botón <strong>Registrar</strong> se prende cuando la ubicación está fina ("Ubicación lista ✓"); si en unos segundos no mejora, se prende igual.</li>
                <li>Toca <strong>Registrar</strong> y pa' arriba.</li>
            </ol>
            <p>La app te lleva a la página del tag y te cuenta qué pasó:</p>
            <ul>
                <li><strong>"Grafiti registrado"</strong>: era un muro nuevo, el tag suma uno más.</li>
                <li><strong>"Este grafiti ya estaba registrado"</strong>: alguien lo spotteó antes que tú a menos de 20 metros. Tu foto se suma a ese muro, pero no cuenta doble.</li>
            </ul>
            <p class="manual-tip">Spottea el tag ahí mismo donde está: la ubicación sale de tu celu en ese momento.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/04-cazar-tag.jpg" alt="Pantalla Spotting con la sugerencia de la IA y los tags cercanos" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot" id="tu-tag">
        <div class="manual-text">
            <h2><b>5</b> Going Up</h2>
            <p>Si rayai, para tu firma pa' que todos tus muros queden a tu nombre.</p>
            <ol>
                <li>En el inicio toca <span class="manual-key">Going Up</span>.</li>
                <li>Escribe tu tag.</li>
                <li>Ponle una foto a tu perfil: sácala al tiro o elígela de la galería.</li>
                <li>Toca <strong>Reclamar mi tag</strong>.</li>
            </ol>
            <p>Si los cabros ya habían spotteado muros con tu tag, pasan a tu perfil altiro. Cada cuenta tiene un solo tag y cada tag un solo dueño. Después podís volver acá pa' cambiar la foto, y ver tu perfil en el menú, en <strong>Mi perfil</strong>.</p>
            <p class="manual-tip">¿Algún vivo se paró con tu tag antes que tú? Escríbenos desde <strong>Contacto</strong> y lo revisamos.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/05-crear-tag.jpg" alt="Pantalla Going Up" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot" id="pagina-tag">
        <div class="manual-text">
            <h2><b>6</b> La página de cada tag</h2>
            <p>Cada tag tiene su página. Llegai tocando su nombre en cualquier parte de la app.</p>
            <ul>
                <li><strong>Arriba:</strong> la foto del tag, quién lo paró (o "Tag sin reclamar") y cuántos muros tiene. Debajo, cuántos King le han tirado a sus fotos: con al menos un King, la corona se pinta amarilla.</li>
                <li><strong>Su lugar en el ranking:</strong> al centro, en amarillo, su puesto. A los lados, el que va justo arriba y el que viene justo abajo. Así cachai a quién tenís que pasar y quién te viene pisando los talones.</li>
                <li><strong>Dónde está:</strong> un mapa con un círculo por cada muro.</li>
                <li><strong>Fotos:</strong> todo lo que la gente ha spotteado de ese tag.</li>
            </ul>
        </div>
        <figure class="manual-shots"><img src="/img/manual/06-tag.jpg" alt="Página del tag KAOS" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot two" id="buscar">
        <div class="manual-text">
            <h2><b>7</b> Buscar Tags</h2>
            <p>Toca <span class="manual-key">Buscar Tags</span> en el inicio o <strong>Buscar</strong> en el menú. Hay tres formas de buscar:</p>
            <ul>
                <li><strong>Cerca de ti:</strong> arriba, antes del mapa, sale lo rayado a menos de 100 metros de donde estái, del más cerca al más lejos, con la distancia. Se ven tres a la vez: desliza pal lado pa' ver el resto.</li>
                <li><strong>Por nombre:</strong> escribe un pedazo del tag y toca Buscar. Te salen los que calzan y cuántos muros tiene cada uno.</li>
                <li><strong>En el mapa:</strong> cada círculo es un muro con su foto. Mueve el mapa, acerca con dos dedos y toca un círculo pa' ir a ese tag.</li>
            </ul>
        </div>
        <figure class="manual-shots">
            <img src="/img/manual/08-buscar.jpg" alt="Buscar Tags con Cerca de ti y el mapa" loading="lazy" width="390" height="780">
            <img src="/img/manual/09-buscar-nombre.jpg" alt="Resultados de buscar por nombre" loading="lazy" width="390" height="780">
        </figure>
    </section>

    <section class="manual-step has-shot" id="ranking">
        <div class="manual-text">
            <h2><b>8</b> El ranking</h2>
            <p>En el menú, <strong>Ranking</strong> te muestra los 20 que mandan en la ciudad. El número de la derecha es cuántos muros distintos tiene cada uno. Bajo cada nombre, cuántos 👑 King le han tirado a sus fotos.</p>
            <p>Pa' subir hay que estar en más lugares. Los que tienen más muros arriba, el resto a caminar. Varias fotos del mismo muro no suman.</p>
            <p>Toca cualquier tag pa' ver su página, su mapa y sus fotos.</p>
        </div>
        <figure class="manual-shots"><img src="/img/manual/10-ranking.jpg" alt="Pantalla Ranking" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot" id="fotos">
        <div class="manual-text">
            <h2><b>9</b> King y comentarios</h2>
            <p>Cada foto tiene el botón <strong>👑 King</strong>, en el inicio y al verla en grande:</p>
            <ul>
                <li>Si la pieza está pulenta, tírale King. Tócalo de nuevo pa' quitarlo. Al lado ves cuántos King lleva la foto.</li>
                <li>Toca cualquier foto pa' verla en grande. Bajo el nombre del tag sale su puesto en el ranking; tócalo y te vai al ranking.</li>
                <li>Escribe abajo y toca <strong>Comentar</strong>. Tus comentarios los podís borrar cuando quieras.</li>
                <li>Toca el nombre del tag pa' ir a su página.</li>
            </ul>
        </div>
        <figure class="manual-shots"><img src="/img/manual/11-foto.jpg" alt="Foto con el botón King, el puesto en el ranking y comentarios" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step has-shot" id="mensajes">
        <div class="manual-text">
            <h2><b>10</b> Mis mensajes</h2>
            <p>En el menú, <strong>Mis mensajes</strong> te marca con un número amarillo cuánto te ha llegado. Ahí cae:</p>
            <ul>
                <li><strong>Mensajes de {{ $name }}:</strong> avisos pa' todos o mensajes solo pa' ti.</li>
                <li><strong>👑 King</strong> cuando alguien le tira King a una foto que subiste o a una foto de tu tag.</li>
                <li>Si se arrepiente y lo quita, el aviso se borra.</li>
                <li>Toca un aviso pa' ver la foto.</li>
                <li>Pa' responder, toca <strong>Escribirle a {{ $name }}</strong>, escribe y dale <strong>Enviar</strong>.</li>
            </ul>
        </div>
        <figure class="manual-shots"><img src="/img/manual/12-mensajes.jpg" alt="Mis mensajes con un mensaje de TAGKING y avisos de King" loading="lazy" width="390" height="780"></figure>
    </section>

    <section class="manual-step" id="instalar">
        <h2><b>11</b> Instala la app en tu celu</h2>
        <p>{{ $name }} funciona desde el navegador, sin bajar nada de ninguna tienda y gratis. Pa' tenerla como cualquier app, con la corona en tu pantalla:</p>
        <ul>
            <li><strong>Android (Chrome):</strong> abre tagking.org, toca los tres puntos <span class="manual-key">⋮</span> arriba a la derecha y elige <strong>Agregar a la pantalla principal</strong> o <strong>Instalar app</strong>.</li>
            <li><strong>iPhone (Safari):</strong> abre tagking.org, toca el botón compartir <span class="manual-key">⬆</span> abajo y elige <strong>Agregar a inicio</strong>.</li>
        </ul>
    </section>

    <section class="manual-step" id="preguntas">
        <h2><b>12</b> Preguntas frecuentes</h2>
        <dl class="manual-faq">
            <dt>La app no me ubica.</dt>
            <dd>Activa el GPS del teléfono y dale permiso de ubicación. En iPhone: Ajustes › Privacidad › Localización, actívala y en Safari elige "Al usar la app". En Android: toca el candado junto a tagking.org › Permisos › Ubicación › Permitir. Después toca <strong>Reintentar ubicación</strong>.</dd>

            <dt>Dice "Afinando tu ubicación" y se demora.</dt>
            <dd>El celu está buscando una ubicación fina. Sal a un lugar más abierto o aléjate de los edificios altos y en segundos mejora.</dd>

            <dt>¿Por qué mi tag no sube en el ranking?</dt>
            <dd>Muro es muro. Otra foto del mismo grafiti no suma; tu tag en otro lugar, sí. Sal a rayar y que te spotteen.</dd>

            <dt>Se me olvidó la clave.</dt>
            <dd>Escríbenos desde <strong>Contacto</strong> con tu usuario y te pasamos una nueva.</dd>

            <dt>La app leyó mal el tag de mi foto.</dt>
            <dd>Lo que lee la inteligencia artificial es solo una sugerencia, y con algunos handstyles se enreda. Bórralo y escribe lo que dice de verdad antes de tocar Registrar. Si no alcanza a leerlo, te avisa y lo escribes tú.</dd>

            <dt>Subí un tag mal escrito.</dt>
            <dd>Escríbenos desde <strong>Contacto</strong> y lo revisamos.</dd>

            <dt>Hay una foto que no debería estar ahí.</dt>
            <dd>Avísanos desde <strong>Contacto</strong> y la revisamos.</dd>
        </dl>
        <p class="manual-download"><a class="button secondary" href="{{ route('contact') }}">Escríbenos</a></p>
    </section>

    <p class="manual-note muted small">Las pantallas de este manual usan tags y usuarios de ejemplo.</p>
@endsection
