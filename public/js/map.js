// Mapas con Leaflet y OpenStreetMap
(() => {
    const cfg = window.KINGTAG;

    const escape = (s) => String(s).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    })[c]);

    const pin = (g) => L.marker([g.lat, g.lng], {
        icon: L.divIcon({
            className: 'tag-pin',
            html: `<img src="${escape(g.thumb)}" alt="">`,
            iconSize: [44, 44],
            iconAnchor: [22, 44],
        }),
    }).bindPopup(
        `<a href="${escape(g.tag_url || '#')}"><img src="${escape(g.thumb)}" width="140" alt=""><br><strong>${escape(g.tag)}</strong></a>`,
    );

    const baseMap = (el) => {
        const map = L.map(el).setView(cfg.center, 14);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);
        return map;
    };

    document.querySelectorAll('[data-map]').forEach((el) => {
        const map = baseMap(el);

        // Mapa fijo del perfil de un tag
        if (el.dataset.points) {
            const points = JSON.parse(el.dataset.points);
            const markers = points.map((g) => pin(g).addTo(map));
            if (markers.length) {
                map.fitBounds(L.featureGroup(markers).getBounds(), { maxZoom: 17, padding: [30, 30] });
            }
            return;
        }

        // Mapa de búsqueda: carga los grafitis del área visible
        const layer = L.layerGroup().addTo(map);
        const load = () => {
            const b = map.getBounds();
            const params = new URLSearchParams({
                south: b.getSouth(), north: b.getNorth(), west: b.getWest(), east: b.getEast(),
            });
            fetch(`${cfg.graffitisUrl}?${params}`, { headers: { Accept: 'application/json' } })
                .then((r) => r.json())
                .then((list) => {
                    layer.clearLayers();
                    list.forEach((g) => pin(g).addTo(layer));
                });
        };
        map.on('moveend', load);
        load();

        navigator.geolocation?.getCurrentPosition(
            (pos) => map.setView([pos.coords.latitude, pos.coords.longitude], 16),
            () => {},
            { enableHighAccuracy: true, maximumAge: 30000, timeout: 20000 },
        );
    });

    // Feed de grafitis a menos de 100 metros
    const feed = document.querySelector('[data-nearby]');
    const status = document.querySelector('[data-nearby-status]');
    const retry = document.querySelector('[data-nearby-retry]');
    if (feed) {
        if (!navigator.geolocation) {
            status.textContent = 'Tu navegador no permite obtener la ubicación.';
            return;
        }
        const locate = () => {
            retry.hidden = true;
            status.textContent = 'Buscando tu ubicación…';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const params = new URLSearchParams({ lat: pos.coords.latitude, lng: pos.coords.longitude });
                    fetch(`${cfg.nearbyUrl}?${params}`, { headers: { Accept: 'application/json' } })
                        .then((r) => r.json())
                        .then((list) => {
                            status.textContent = list.length
                                ? `${list.length} grafiti${list.length === 1 ? '' : 's'} a menos de 100 m.`
                                : 'No hay grafitis registrados a menos de 100 m.';
                            feed.innerHTML = list.map((g) => `
                                <a href="${escape(g.tag_url)}">
                                    <img src="${escape(g.thumb)}" alt="${escape(g.tag)}" loading="lazy">
                                    <span>${escape(g.tag)} · ${g.distance} m</span>
                                </a>`).join('');
                        })
                        .catch(() => {
                            status.textContent = 'No pudimos cargar los grafitis cercanos. Revisa tu conexión.';
                            retry.hidden = false;
                        });
                },
                (err) => {
                    status.textContent = window.kingtagGeoError
                        ? window.kingtagGeoError(err)
                        : 'Activa tu ubicación para ver los grafitis cercanos.';
                    retry.hidden = false;
                },
                { enableHighAccuracy: true, maximumAge: 30000, timeout: 20000 },
            );
        };
        retry.addEventListener('click', locate);
        locate();
    }
})();
