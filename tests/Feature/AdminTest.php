<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Graffiti;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->admin = User::factory()->create(['username' => 'jefe']);
        $this->admin->forceFill(['is_admin' => true])->save();
    }

    private function register(User $user, string $text, float $lat = -33.4489): void
    {
        $this->actingAs($user)->post('/registrar', [
            'text' => $text,
            'lat' => $lat,
            'lng' => -70.6693,
            'photo' => UploadedFile::fake()->image('tag.jpg', 800, 600),
        ]);
    }

    public function test_only_admins_can_open_the_panel(): void
    {
        $this->get('/admin')->assertRedirect('/entrar');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $user = User::factory()->create();
        $this->register($user, 'KASE');
        $this->actingAs($user)->delete('/admin/fotos/'.Photo::sole()->id)->assertForbidden();
        $this->assertSame(1, Photo::count());

        foreach (['/admin', '/admin/mensajes', '/admin/tags', '/admin/usuarios'] as $url) {
            $this->actingAs($this->admin)->get($url)->assertOk();
        }
    }

    public function test_the_menu_shows_the_panel_only_to_admins(): void
    {
        $this->actingAs(User::factory()->create())->get('/')->assertDontSee('Administrar');
        $this->actingAs($this->admin)->get('/')->assertSee('Administrar');
    }

    public function test_deleting_the_main_photo_promotes_another_one(): void
    {
        $user = User::factory()->create();
        $this->register($user, 'KASE');
        $this->register($user, 'KASE');

        $graffiti = Graffiti::sole();
        $main = $graffiti->photos()->where('path', $graffiti->photo)->sole();
        $other = $graffiti->photos()->whereKeyNot($main->id)->sole();

        $this->actingAs($this->admin)->delete("/admin/fotos/{$main->id}")->assertRedirect();

        $graffiti->refresh();
        $this->assertSame($other->path, $graffiti->photo);
        $this->assertSame($other->thumb, $graffiti->thumb);
        $this->assertSame(1, $graffiti->reports);
        Storage::disk('public')->assertMissing([$main->path, $main->thumb]);
        Storage::disk('public')->assertExists([$other->path, $other->thumb]);
    }

    public function test_deleting_the_last_photo_removes_the_graffiti(): void
    {
        $this->register(User::factory()->create(), 'KASE');

        $this->actingAs($this->admin)->delete('/admin/fotos/'.Photo::sole()->id);

        $this->assertSame(0, Graffiti::count());
        $this->assertSame(1, Tag::count());
    }

    public function test_deleting_a_tag_removes_its_graffitis_photos_and_files(): void
    {
        $user = User::factory()->create();
        $this->register($user, 'KASE');
        $this->register($user, 'KASE', -33.46);
        $this->register($user, 'OTRO');
        $files = Photo::all()->flatMap(fn (Photo $p) => [$p->path, $p->thumb])->all();

        $tag = Tag::firstWhere('normalized', 'KASE');
        $this->actingAs($this->admin)->delete("/admin/tags/{$tag->id}")->assertRedirect('/admin/tags');

        $this->assertSame(['OTRO'], Tag::pluck('normalized')->all());
        $this->assertSame(1, Graffiti::count());
        $this->assertSame(1, Photo::count());
        Storage::disk('public')->assertMissing(array_slice($files, 0, 4));
        Storage::disk('public')->assertExists(array_slice($files, 4));
    }

    public function test_unclaiming_a_tag_keeps_its_graffitis(): void
    {
        $artist = User::factory()->create();
        $this->register($artist, 'KASE');
        $tag = Tag::sole();
        $tag->update(['artist_id' => $artist->id]);

        $this->actingAs($this->admin)->post("/admin/tags/{$tag->id}/liberar")->assertRedirect();

        $this->assertNull($tag->fresh()->artist_id);
        $this->assertSame(1, Graffiti::count());
    }

    public function test_admin_can_reset_a_password_and_delete_accounts_but_not_their_own(): void
    {
        $user = User::factory()->create(['username' => 'kase', 'password' => 'vieja123']);

        $this->actingAs($this->admin)->post("/admin/usuarios/{$user->id}/clave")
            ->assertSessionHas('status', fn ($s) => str_starts_with($s, 'Nueva clave para kase: '));
        $this->assertFalse(auth()->validate(['username' => 'kase', 'password' => 'vieja123']));

        $this->actingAs($this->admin)->delete("/admin/usuarios/{$this->admin->id}")->assertStatus(422);
        $this->actingAs($this->admin)->delete("/admin/usuarios/{$user->id}")->assertRedirect();
        $this->assertModelMissing($user);
        $this->assertModelExists($this->admin);
    }

    public function test_anyone_can_send_a_contact_message_and_admins_read_it(): void
    {
        $this->post('/contacto', ['message' => ''])->assertSessionHasErrors('message');

        $this->post('/contacto', [
            'name' => 'Ana',
            'contact' => '@ana',
            'message' => 'Hay una foto que no es un grafiti.',
        ])->assertRedirect('/');

        $message = ContactMessage::sole();
        $this->assertNull($message->read_at);

        $this->actingAs($this->admin)->get('/admin')->assertSee('1 sin leer');
        $this->actingAs($this->admin)->get('/admin/mensajes')->assertSee('Hay una foto que no es un grafiti.');

        $this->actingAs($this->admin)->post("/admin/mensajes/{$message->id}/leido");
        $this->assertNotNull($message->fresh()->read_at);

        $this->actingAs($this->admin)->delete("/admin/mensajes/{$message->id}");
        $this->assertModelMissing($message);
    }

    public function test_admin_command(): void
    {
        $user = User::factory()->create(['username' => 'andres']);

        $this->artisan('kingtag:admin', ['username' => 'andres'])->assertSuccessful();
        $this->assertTrue($user->fresh()->is_admin);

        $this->artisan('kingtag:admin', ['username' => 'andres', '--quitar' => true])->assertSuccessful();
        $this->assertFalse($user->fresh()->is_admin);

        $this->artisan('kingtag:admin', ['username' => 'nadie'])->assertFailed();
    }

    public function test_users_cannot_make_themselves_admin_when_registering(): void
    {
        $this->post('/registro', [
            'username' => 'pillo',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'is_admin' => 1,
        ]);

        $this->assertFalse(User::firstWhere('username', 'pillo')->is_admin);
    }
}
