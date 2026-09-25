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

// Ubicación para "Registrar tag"
document.querySelectorAll('[data-geo-form]').forEach((form) => {
    const lat = form.querySelector('[data-lat]');
    const lng = form.querySelector('[data-lng]');
    const status = form.querySelector('[data-geo-status]');

    if (!('geolocation' in navigator)) {
        status.textContent = 'Tu navegador no permite obtener la ubicación.';
        return;
    }

    navigator.geolocation.watchPosition(
        (pos) => {
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
