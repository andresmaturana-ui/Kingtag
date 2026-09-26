<?php

namespace Tests\Feature;

use App\Models\InboxMessage;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\User;
use App\Models\VoteNotice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InboxTest extends TestCase
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

    public function test_only_admins_can_send_messages(): void
    {
        $this->get('/mensajes')->assertRedirect('/entrar');
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/bandeja')->assertForbidden();
        $this->actingAs($user)->post('/admin/bandeja/todos', ['body' => 'hola'])->assertForbidden();
        $this->actingAs($user)->post("/admin/usuarios/{$user->id}/mensajes", ['body' => 'hola'])->assertForbidden();
        $this->assertSame(0, InboxMessage::count());
    }

    public function test_admin_writes_to_one_user_who_sees_it_and_replies(): void
    {
        $user = User::factory()->create(['username' => 'neko']);
        $other = User::factory()->create();

        $this->actingAs($this->admin)->post("/admin/usuarios/{$user->id}/mensajes", ['body' => 'Hola Neko'])
            ->assertRedirect("/admin/usuarios/{$user->id}/mensajes");

        $this->actingAs($other)->get('/')->assertDontSee('burger-dot', false);
        $this->actingAs($user)->get('/')->assertSee('Mis mensajes')->assertSee('burger-dot', false)->assertSee('count-badge">1<', false);
        $this->actingAs($user)->get('/mensajes')->assertOk()->assertSee('Hola Neko');
        $this->actingAs($user)->get('/')->assertDontSee('burger-dot', false);
        $this->actingAs($other)->get('/mensajes')->assertDontSee('Hola Neko');

        $this->actingAs($user)->post('/mensajes', ['body' => 'Gracias!'])->assertRedirect('/mensajes');
        $this->actingAs($this->admin)->get('/admin/bandeja')->assertSee('neko')->assertSee('1 nueva');
        $this->actingAs($this->admin)->get("/admin/usuarios/{$user->id}/mensajes")->assertSee('Hola Neko')->assertSee('Gracias!');
        $this->actingAs($this->admin)->get('/admin/bandeja')->assertDontSee('1 nueva');
    }

    public function test_admin_sends_a_notice_to_everyone(): void
    {
        $users = User::factory()->count(3)->create();

        $this->actingAs($this->admin)->post('/admin/bandeja/todos', ['body' => 'Nueva versión'])
            ->assertSessionHas('status', 'Aviso enviado a 3 usuarios.');

        foreach ($users as $user) {
            $this->actingAs($user)->get('/mensajes')->assertSee('Nueva versión')->assertSee('para todos');
        }
        $this->actingAs($this->admin)->get('/mensajes')->assertDontSee('Nueva versión');
        $this->actingAs($this->admin)->get('/admin/bandeja')->assertSee('leído por 3 de 3');
    }

    public function test_king_and_toy_reach_the_photographer_and_the_tag_owner(): void
    {
        $artist = User::factory()->create(['username' => 'kaos']);
        $spotter = User::factory()->create(['username' => 'cazador']);
        $voter = User::factory()->create(['username' => 'fan']);

        $this->actingAs($spotter)->post('/registrar', [
            'text' => 'KAOS', 'lat' => -33.4489, 'lng' => -70.6693,
            'photo' => UploadedFile::fake()->image('tag.jpg', 800, 600),
        ]);
        Tag::sole()->update(['artist_id' => $artist->id]);
        $photo = Photo::sole();

        $this->actingAs($voter)->post("/fotos/{$photo->id}/king");
        $this->actingAs($spotter)->get('/mensajes')->assertSee('fan')->assertSee('a tu foto de');
        $this->actingAs($artist)->get('/mensajes')->assertSee('a una foto de tu tag');

        // Cambiar a Toy reemplaza el aviso; quitarlo lo borra.
        $this->actingAs($voter)->post("/fotos/{$photo->id}/toy");
        $this->assertSame(['toy', 'toy'], VoteNotice::pluck('kind')->all());
        $this->actingAs($voter)->post("/fotos/{$photo->id}/toy");
        $this->assertSame(0, VoteNotice::count());

        // El voto propio no genera aviso.
        $this->actingAs($spotter)->post("/fotos/{$photo->id}/king");
        $this->assertSame(['kaos'], VoteNotice::with('photo')->get()->map(fn ($n) => User::find($n->user_id)->username)->all());
    }

    public function test_admin_panel_keeps_contact_messages_under_contacto(): void
    {
        $this->actingAs($this->admin)->get('/admin')->assertSee('Contacto')->assertSee('Mensajes');
        $this->actingAs($this->admin)->get('/admin/mensajes')->assertOk()->assertSee('<h1>Contacto</h1>', false);
        $this->actingAs($this->admin)->get('/admin/usuarios')->assertSee('/admin/usuarios/'.$this->admin->id.'/mensajes', false);
    }
}
