# TAGKING

App para registrar tags y grafitis geolocalizados en la ciudad y armar un
ranking con los tags más grafiteados.

## Qué hace

- **Ingresa tu tag:** el artista reclama su tag con su cuenta y sube una foto.
  Todos los grafitis que otras personas registraron con ese texto pasan a su perfil.
- **Registrar tag:** cualquier persona con cuenta le saca una foto a un tag en la
  calle y escribe lo que dice. Se guarda la ubicación GPS.
- **Registro único:** si alguien registra el mismo tag a menos de 20 m de uno que
  ya existe, la foto se suma a ese grafiti en vez de crear uno nuevo.
- **Perfil del tag:** incluye:
  - un mapa de sus grafitis;
  - un feed de fotos;
  - su puesto en el ranking, con los tags que están justo arriba y justo abajo.
- **Buscar:** mapa con pines y fotos, búsqueda por texto y grafitis a menos de 100 m.
- **Ranking:** los 20 tags con más grafitis distintos.
- **Menú hamburguesa** arriba a la derecha.
- Se puede **instalar en el celular** como una app (PWA).

Las cuentas son solo usuario y clave, sin correo.

## Tecnología

- Laravel 13 (PHP 8.3 o más nuevo) con MySQL. Corre en el plan Premium Web Hosting de Hostinger.
- Pantallas en Blade con CSS y JavaScript simples. No hay que compilar nada ni instalar Node.
- Mapas con Leaflet y OpenStreetMap. Leaflet viene incluido en `public/vendor/leaflet`.
- Las fotos se enderezan, se achican y se guardan sin datos EXIF.

## Instalar en Hostinger

Ver [docs/INSTALAR-EN-HOSTINGER.md](docs/INSTALAR-EN-HOSTINGER.md).

## Probar en tu computador

Necesitas PHP 8.3 o más nuevo y Composer.

```sh
composer setup
php artisan serve
```

Abre http://localhost:8000. Por defecto usa SQLite, así que no hace falta instalar MySQL.

Para correr las pruebas:

```sh
php artisan test
```
