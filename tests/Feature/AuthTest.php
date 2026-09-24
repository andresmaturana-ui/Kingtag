<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_someone_can_create_an_account_with_only_username_and_password(): void
    {
        $this->post('/registro', [
            'username' => 'kase',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['username' => 'kase']);
    }

    public function test_usernames_are_unique(): void
    {
        User::factory()->create(['username' => 'kase']);

        $this->post('/registro', [
            'username' => 'kase',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertSessionHasErrors('username');
    }

    public function test_login_and_logout(): void
    {
        User::factory()->create(['username' => 'kase', 'password' => 'secreto123']);

        $this->post('/entrar', ['username' => 'kase', 'password' => 'mala'])->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->post('/entrar', ['username' => 'kase', 'password' => 'secreto123'])->assertRedirect('/');
        $this->assertAuthenticated();

        $this->post('/salir')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_registering_a_tag_requires_an_account(): void
    {
        $this->get('/registrar')->assertRedirect('/entrar');
        $this->get('/mi-tag')->assertRedirect('/entrar');
    }

    public function test_reset_password_command(): void
    {
        $user = User::factory()->create(['username' => 'kase', 'password' => 'vieja123']);

        $this->artisan('kingtag:reset-clave', ['username' => 'kase'])->assertSuccessful();

        $this->assertFalse(password_verify('vieja123', $user->fresh()->password));
    }
}
