<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

// Como las cuentas no tienen correo, las claves olvidadas se resetean a mano:
// php artisan kingtag:reset-clave nombre_de_usuario
Artisan::command('kingtag:reset-clave {username}', function (string $username) {
    $user = User::firstWhere('username', $username);

    if (! $user) {
        $this->error("No existe el usuario {$username}.");

        return 1;
    }

    $password = Str::password(10, symbols: false);
    $user->update(['password' => $password]);

    $this->info("Nueva clave para {$username}: {$password}");
})->purpose('Genera una clave nueva para un usuario que olvidó la suya');

// Da o quita permisos de administrador:
// php artisan kingtag:admin nombre_de_usuario
// php artisan kingtag:admin nombre_de_usuario --quitar
Artisan::command('kingtag:admin {username} {--quitar}', function (string $username) {
    $user = User::firstWhere('username', $username);

    if (! $user) {
        $this->error("No existe el usuario {$username}.");

        return 1;
    }

    $admin = ! $this->option('quitar');
    $user->forceFill(['is_admin' => $admin])->save();

    $this->info($admin
        ? "{$username} ahora es administrador. Entra a /admin."
        : "{$username} ya no es administrador.");
})->purpose('Da o quita permisos de administrador a un usuario');
