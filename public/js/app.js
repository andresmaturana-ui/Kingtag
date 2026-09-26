// Menú hamburguesa
(() => {
    const burger = document.querySelector('[data-burger]');
    const menu = document.getElementById('menu');
    if (!burger || !menu) return;

    const setOpen = (open) => {
        menu.hidden = !open;
        burger.setAttribute('aria-expanded', String(open));
        document.body.classList.toggle('menu-open', open);
    };

    burger.addEventListener('click', () => setOpen(menu.hidden));
    document.addEventListener('keydown', (e) => e.key === 'Escape' && setOpen(false));
})();

// Foto: vista previa y "elegir de la galería"
document.querySelectorAll('[data-photo-form]').forEach((form) => {
    const input = form.querySelector('[data-photo-input]');
    const gallery = form.querySelector('[data-photo-gallery]');
    const preview = form.querySelector('[data-photo-preview]');

    const show = () => {
        const file = input.files[0];
        if (!file) return;
        preview.src = URL.createObjectURL(file);
        preview.hidden = false;
        form.dispatchEvent(new CustomEvent('kingtag:photo', { detail: file }));
    };

    input.addEventListener('change', show);
    gallery?.addEventListener('change', () => {
        // Se envía siempre el mismo campo "photo", venga de la cámara o de la galería.
        input.files = gallery.files;
        show();
    });

    form.addEventListener('submit', (e) => {
        if (!input.files.length) {
            e.preventDefault();
            alert('Falta la foto del tag.');
        }
    });
});

// Mensaje para cada motivo por el que el teléfono no entrega la ubicación
window.kingtagGeoError = (err) => {
    if (err.code === 1) {
        return 'Tu teléfono no le da permiso de ubicación a TAGKING. '
            + 'iPhone: Ajustes › Privacidad › Localización › activa la localización y en Safari elige "Al usar la app". '
            + 'Android: toca el candado junto a tagking.cl › Permisos › Ubicación › Permitir. Después toca Reintentar.';
    }
    if (err.code === 2) {
        return 'El teléfono no encuentra tu ubicación. Revisa que el GPS (Ubicación) esté activado y toca Reintentar.';
    }
    return 'La ubicación se está demorando. Sal a un lugar más abierto o toca Reintentar.';
};

// Ubicación para "Spotting". La primera lectura del teléfono suele venir del
// wifi o la antena y puede fallar por cientos de metros, así que esperamos
// una lectura precisa antes de dejar registrar.
document.querySelectorAll('[data-geo-form]').forEach((form) => {
    const lat = form.querySelector('[data-lat]');
    const lng = form.querySelector('[data-lng]');
    const accuracy = form.querySelector('[data-accuracy]');
    const status = form.querySelector('[data-geo-status]');
    const retry = form.querySelector('[data-geo-retry]');
    const submit = form.querySelector('[data-geo-submit]');

    const GOOD = 30;        // metros: con esto ya se puede registrar
    const MAX_WAIT = 15000; // si no mejora en este tiempo, dejamos registrar igual

    if (!('geolocation' in navigator)) {
        status.textContent = 'Tu navegador no permite obtener la ubicación.';
        return;
    }

    let best = null;
    let watchId = null;
    let startedAt = 0;
    let waitTimer = null;
    let lastAnnounced = null;

    const ready = () => best && (best.accuracy <= GOOD || Date.now() - startedAt >= MAX_WAIT);

    const render = () => {
        if (!best) return;
        const m = Math.round(best.accuracy);
        if (best.accuracy <= GOOD) {
            status.textContent = `Ubicación lista ✓ (precisión de ${m} m).`;
        } else if (ready()) {
            status.textContent = `Ubicación aproximada (precisión de ${m} m). Puedes registrar, pero si sales a un lugar más abierto puede mejorar.`;
        } else {
            status.textContent = `Afinando tu ubicación… precisión de ${m} m.`;
        }
        submit.disabled = !ready();
    };

    const announce = () => {
        // Avisamos la primera vez y cada vez que la precisión mejora harto,
        // para que las sugerencias cercanas se calculen con la mejor lectura.
        if (lastAnnounced && best.accuracy > lastAnnounced.accuracy / 2) return;
        lastAnnounced = best;
        form.dispatchEvent(new CustomEvent('kingtag:location', { detail: best }));
    };

    const start = () => {
        if (watchId !== null) navigator.geolocation.clearWatch(watchId);
        clearTimeout(waitTimer);
        best = null;
        lastAnnounced = null;
        startedAt = Date.now();
        lat.value = lng.value = accuracy.value = '';
        submit.disabled = true;
        retry.hidden = true;
        status.textContent = 'Buscando tu ubicación…';
        waitTimer = setTimeout(render, MAX_WAIT);

        watchId = navigator.geolocation.watchPosition(
            (pos) => {
                const c = pos.coords;
                // Nos quedamos con la lectura más precisa; una nueva la reemplaza
                // si es casi igual de buena (la persona se pudo mover).
                if (!best || c.accuracy <= best.accuracy * 1.2) {
                    best = { latitude: c.latitude, longitude: c.longitude, accuracy: c.accuracy };
                    lat.value = c.latitude.toFixed(7);
                    lng.value = c.longitude.toFixed(7);
                    accuracy.value = Math.round(c.accuracy);
                    announce();
                }
                retry.hidden = true;
                render();
            },
            (err) => {
                // Si ya tenemos una lectura, un corte momentáneo no importa.
                if (best && err.code !== 1) return;
                status.textContent = window.kingtagGeoError(err);
                retry.hidden = false;
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 },
        );
    };

    retry.addEventListener('click', start);
    start();

    form.addEventListener('submit', (e) => {
        if (!lat.value || !lng.value) {
            e.preventDefault();
            alert('Todavía no tenemos tu ubicación. Espera unos segundos.');
        }
    });
});

// Spotting: sugerir el texto del tag
document.querySelectorAll('[data-geo-form]').forEach((form) => {
    const text = form.querySelector('[data-tag-text]');
    if (!text) return;

    // Lo que puso la app (no la persona). Si la persona escribe, no se lo pisamos.
    let autoValue = '';
    const suggest = (value) => {
        if (text.value.trim() === '' || text.value === autoValue) {
            text.value = value;
            autoValue = value;
        }
    };
    text.addEventListener('input', () => { autoValue = ''; markChips(); });

    // 1) Tags que ya están registrados a menos de 100 m
    const box = form.querySelector('[data-tag-suggest]');
    const chips = form.querySelector('[data-tag-chips]');
    const markChips = () => chips?.querySelectorAll('.chip').forEach((c) => {
        c.classList.toggle('selected', c.textContent === text.value);
    });

    let request = 0;
    form.addEventListener('kingtag:location', async (e) => {
        if (!box) return;
        const mine = ++request;
        try {
            const url = `${box.dataset.url}?lat=${e.detail.latitude}&lng=${e.detail.longitude}`;
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) return;
            const list = await res.json();
            if (mine !== request) return; // llegó una ubicación mejor mientras tanto
            const names = [...new Set(list.map((g) => g.tag))].slice(0, 12);
            chips.replaceChildren();
            box.hidden = !names.length;
            if (!names.length) return;
            names.forEach((name) => {
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'chip';
                chip.textContent = name;
                chip.addEventListener('click', () => {
                    text.value = name;
                    autoValue = '';
                    markChips();
                });
                chips.append(chip);
            });
            box.hidden = false;
            markChips();
        } catch {
            // Sin señal: no mostramos sugerencias.
        }
    });

    // 2) La IA lee el tag en la foto
    const reader = form.querySelector('[data-tag-reader]');
    if (!reader) return;
    let current = 0;

    // Achica la foto antes de mandarla: sube más rápido y la IA cobra menos.
    const shrink = async (file) => {
        const img = await createImageBitmap(file);
        const scale = Math.min(1, 1024 / Math.max(img.width, img.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(img.width * scale);
        canvas.height = Math.round(img.height * scale);
        canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
        return new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.8));
    };

    form.addEventListener('kingtag:photo', async (e) => {
        const attempt = ++current;
        reader.hidden = false;
        reader.textContent = 'Leyendo el tag de la foto…';
        try {
            const body = new FormData();
            body.append('photo', await shrink(e.detail), 'tag.jpg');
            body.append('_token', form.querySelector('[name=_token]').value);
            const res = await fetch(reader.dataset.url, { method: 'POST', body, headers: { Accept: 'application/json' } });
            if (attempt !== current) return;
            const data = res.ok ? await res.json() : {};
            if (data.text) {
                suggest(data.text);
                markChips();
                reader.innerHTML = '';
                reader.append('Parece que dice ');
                const strong = document.createElement('strong');
                strong.textContent = data.text;
                reader.append(strong, '. Revisa que esté bien antes de registrar.');
            } else {
                reader.textContent = 'No pudimos leer el tag. Escríbelo tú.';
            }
        } catch {
            if (attempt === current) reader.textContent = 'No pudimos leer el tag. Escríbelo tú.';
        }
    });
});

// Pide confirmación antes de acciones que no se pueden deshacer
document.addEventListener('submit', (e) => {
    const message = e.target.dataset?.confirm;
    if (message && !confirm(message)) e.preventDefault();
});

// App instalable
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
}

// Inicio: el feed "Lo último" trae más fotos solo, al acercarse al final
(() => {
    const feed = document.querySelector('[data-feed]');
    const more = document.querySelector('[data-feed-more]');
    if (!feed || !more || !('IntersectionObserver' in window)) return;

    let loading = false;
    const observer = new IntersectionObserver(async (entries) => {
        const link = more.querySelector('a');
        if (!entries[0].isIntersecting || loading || !link) return;
        loading = true;
        link.textContent = 'Cargando…';
        try {
            const res = await fetch(link.href, { headers: { Accept: 'text/html' } });
            if (!res.ok) throw new Error(res.status);
            const page = new DOMParser().parseFromString(await res.text(), 'text/html');
            page.querySelectorAll('[data-feed] > *').forEach((item) => feed.append(item));
            const next = page.querySelector('[data-feed-more] a');
            if (next) {
                link.href = next.href;
                link.textContent = 'Ver más';
                // Si el final sigue a la vista, esto hace que se pida la página siguiente.
                observer.unobserve(more);
                observer.observe(more);
            } else {
                observer.disconnect();
                more.remove();
            }
        } catch {
            // Sin señal: queda el botón para intentarlo a mano.
            link.textContent = 'Ver más';
        }
        loading = false;
    }, { rootMargin: '600px 0px' });

    observer.observe(more);
})();

// King y Toy: se marcan sin recargar la página (así el feed no pierde el lugar)
document.addEventListener('submit', async (e) => {
    const form = e.target.closest('[data-vote]');
    if (!form) return;
    e.preventDefault();
    const box = form.closest('[data-votes]');
    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json' },
        });
        if (!res.ok) throw new Error(res.status);
        const data = await res.json();
        [['king', data.king, data.kinged], ['toy', data.toy, data.toyed]].forEach(([vote, count, on]) => {
            const button = box.querySelector(`.vote.${vote}`);
            button.classList.toggle('on', on);
            button.setAttribute('aria-pressed', String(on));
            button.querySelector('[data-count]').textContent = count;
        });
    } catch {
        form.submit();
    }
});
