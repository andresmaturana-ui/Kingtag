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
