# Instalar Kingtag en Hostinger (plan Premium Web Hosting)

Esta guía la haces una sola vez. Toma unos 20 minutos. Donde dice
`tudominio.cl`, pon tu dominio real.

## 1. Preparar el hosting en hPanel

1. **Versión de PHP:** ve a *Sitios web → Administrar → Avanzado → Configuración de PHP* y elige **PHP 8.3 o más nuevo**.
   En la pestaña *Extensiones de PHP* revisa que estén marcadas `gd`, `exif`, `fileinfo`, `pdo_mysql` e `intl`.
2. **Tamaño de las fotos:** en esa misma pantalla, en *Opciones de PHP*, deja `upload_max_filesize` y `post_max_size` en **16M** o más.
   Deja también `memory_limit` en **256M** o más, porque la app achica las fotos del celular en el servidor.
3. **Base de datos:** ve a *Bases de datos → Bases de datos MySQL* y crea una.
   Anota el **nombre de la base**, el **usuario** y la **contraseña**. Hostinger les agrega un prefijo, por ejemplo `u123456789_kingtag`.
4. **Acceso SSH:** ve a *Avanzado → Acceso SSH*, actívalo y anota el comando de conexión. Se ve así: `ssh -p 65002 u123456789@123.45.67.89`.
5. **SSL (candado https):** ve a *Seguridad → SSL* y actívalo para tu dominio.
   Sin https, el celular no deja usar la cámara ni el GPS, y la app no se puede instalar.

## 2. Subir el código

Conéctate por SSH desde la terminal (en Windows puedes usar PowerShell) y ejecuta:

```sh
cd ~/domains/tudominio.cl
git clone https://github.com/andresmaturana-ui/Kingtag.git kingtag
cd kingtag
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

## 3. Configurar

Abre el archivo de configuración:

```sh
nano .env
```

Cambia estas líneas con tus datos. Para guardar: `Ctrl+O`, `Enter` y luego `Ctrl+X`.

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.cl

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_kingtag
DB_USERNAME=u123456789_kingtag
DB_PASSWORD=la-contraseña-de-la-base
```

Las líneas `DB_...` empiezan con `#`: bórralo para activarlas.

Si tu ciudad no es Santiago, cambia también `KINGTAG_MAP_LAT` y `KINGTAG_MAP_LNG`.
Ese es el punto donde se abre el mapa cuando no sabemos la ubicación de la persona.

## 4. Crear las tablas y publicar

```sh
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Ahora haz que el dominio muestre la carpeta `public` de Kingtag:

```sh
cd ~/domains/tudominio.cl
mv public_html public_html_viejo
ln -s kingtag/public public_html
```

Abre `https://tudominio.cl` en el celular. Deberías ver los tres botones.

## 5. Instalar la app en el celular

- **Android (Chrome):** menú ⋮ → *Agregar a la pantalla principal* o *Instalar app*.
- **iPhone (Safari):** botón Compartir → *Agregar a pantalla de inicio*.

## Actualizar cuando haya cambios

Cuando haya código nuevo en GitHub:

```sh
cd ~/domains/tudominio.cl/kingtag
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Si alguien olvida su clave

Las cuentas no tienen correo, así que la clave se resetea a mano por SSH:

```sh
cd ~/domains/tudominio.cl/kingtag
php artisan kingtag:reset-clave nombre_de_usuario
```

El comando muestra la clave nueva. Mándasela a la persona.

## Problemas comunes

| Síntoma | Qué revisar |
|---|---|
| Error 500 | Revisa `storage/logs/laravel.log`, casi siempre dice qué pasó. Por un momento puedes poner `APP_DEBUG=true` en `.env` y correr `php artisan config:cache`, pero vuelve a dejarlo en `false`. |
| Las fotos no se ven | Corre `php artisan storage:link` y revisa que `APP_URL` tenga tu dominio con `https`. |
| "No pudimos obtener tu ubicación" | El sitio tiene que abrirse con `https` y el celular tiene que darle permiso de ubicación al navegador. |
| No deja subir fotos grandes | Sube `upload_max_filesize`, `post_max_size` y `memory_limit` (paso 1.2). |
