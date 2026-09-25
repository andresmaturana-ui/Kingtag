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

// Ubicación para "Spotting"
document.querySelectorAll('[data-geo-form]').forEach((form) => {
    const lat = form.querySelector('[data-lat]');
    const lng = form.querySelector('[data-lng]');
    const status = form.querySelector('[data-geo-status]');

    if (!('geolocation' in navigator)) {
        status.textContent = 'Tu navegador no permite obtener la ubicación.';
        return;
    }

    let announced = false;
    navigator.geolocation.watchPosition(
        (pos) => {
            if (!announced) {
                announced = true;
                form.dispatchEvent(new CustomEvent('kingtag:location', { detail: pos.coords }));
            }
            lat.value = pos.coords.latitude.toFixed(7);
            lng.value = pos.coords.longitude.toFixed(7);
            status.textContent = `Ubicación lista (precisión de ${Math.round(pos.coords.accuracy)} m).`;
        },
        () => {
            status.textContent = 'No pudimos obtener tu ubicación. Activa el GPS y dale permiso a la app.';
        },
        { enableHighAccuracy: true, maximumAge: 10000, timeout: 20000 },
    );

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

    form.addEventListener('kingtag:location', async (e) => {
        if (!box) return;
        try {
            const url = `${box.dataset.url}?lat=${e.detail.latitude}&lng=${e.detail.longitude}`;
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) return;
            const names = [...new Set((await res.json()).map((g) => g.tag))].slice(0, 12);
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
