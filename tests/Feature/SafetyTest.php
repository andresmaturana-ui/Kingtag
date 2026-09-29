<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Lo que pide Google Play: privacidad, borrar la cuenta, reportar y bloquear.
 */
class SafetyTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    private Photo $photo;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->author = User::factory()->create(['username' => 'pintor', 'password' => 'secreta1']);
        $this->actingAs($this->author)->post('/registrar', [
            'text' => 'KASE',
            'lat' => -33.4489,
            'lng' => -70.6693,
            'photo' => UploadedFile::fake()->image('tag.jpg', 800, 600),
        ]);
        auth()->logout();
        $this->photo = Photo::sole();
    }

    public function test_privacy_and_delete_account_pages_are_public(): void
    {
        $this->get('/privacidad')->assertOk()->assertSee('Qué datos guardamos')->assertSee('Reglas de la comunidad');
        $this->get('/borrar-cuenta')->assertOk()->assertSee('Entrar para borrar mi cuenta');
    }

    public function test_deleting_the_account_needs_the_password(): void
    {
        $this->actingAs($this->author)->delete('/borrar-cuenta', ['password' => 'otra'])->assertSessionHasErrors('password');
        $this->assertModelExists($this->author);
    }

    public function test_deleting_the_account_keeps_photos_without_author_by_default(): void
    {
        $other = User::factory()->create();
        Comment::create(['photo_id' => $this->photo->id, 'user_id' => $this->author->id, 'body' => 'hola']);
        $this->photo->likers()->attach($this->author);
        $other->blocked()->attach($this->author);

        $this->actingAs($this->author)->delete('/borrar-cuenta', ['password' => 'secreta1'])->assertRedirect('/');

        $this->assertGuest();
        $this->assertModelMissing($this->author);
        $this->assertNull($this->photo->fresh()->user_id);
        $this->assertSame(0, Comment::count());
        $this->assertSame(0, $this->photo->likers()->count());
        $this->assertSame([], $other->fresh()->blockedIds());
    }

    public function test_deleting_the_account_can_delete_photos_and_frees_the_tag(): void
    {
        Tag::sole()->update(['artist_id' => $this->author->id]);

        $this->actingAs($this->author)->delete('/borrar-cuenta', ['password' => 'secreta1', 'delete_photos' => '1']);

        $this->assertSame(0, Photo::count());
        Storage::disk('public')->assertMissing($this->photo->path);
        $this->assertNull(Tag::sole()->artist_id);
    }

    public function test_users_report_photos_and_comments_and_admins_review_them(): void
    {
        $reader = User::factory()->create();
        $comment = Comment::create(['photo_id' => $this->photo->id, 'user_id' => $this->author->id, 'body' => 'feo']);

        $this->post("/fotos/{$this->photo->id}/reportar", ['reason' => 'spam'])->assertRedirect('/entrar');
        $this->actingAs($reader)->post("/fotos/{$this->photo->id}/reportar", ['reason' => 'inventado'])->assertSessionHasErrors('reason');
        $this->actingAs($reader)->post("/fotos/{$this->photo->id}/reportar", ['reason' => 'spam'])->assertRedirect("/fotos/{$this->photo->id}");
        $this->actingAs($reader)->post("/fotos/{$this->photo->id}/reportar", ['reason' => 'ofensivo']);
        $this->actingAs($reader)->post("/comentarios/{$comment->id}/reportar", ['reason' => 'acoso']);
        $this->assertSame(2, Report::count());

        $this->actingAs($reader)->get('/admin/reportes')->assertForbidden();

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get('/admin')->assertSee('2 pendientes');
        $this->actingAs($admin)->get('/admin/reportes')->assertOk()->assertSee('Ofensivo o violento')->assertSee('feo');

        $report = Report::whereNotNull('photo_id')->sole();
        $this->actingAs($admin)->post("/admin/reportes/{$report->id}/listo");
        $this->assertNotNull($report->fresh()->resolved_at);
    }

    public function test_blocking_hides_photos_and_comments_until_unblocked(): void
    {
        $me = User::factory()->create();
        $commenter = User::factory()->create(['username' => 'molesto']);
        Comment::create(['photo_id' => $this->photo->id, 'user_id' => $commenter->id, 'body' => 'comentario pesado']);

        $this->actingAs($me)->get("/fotos/{$this->photo->id}")->assertSee('comentario pesado');
        $this->actingAs($me)->post("/usuarios/{$commenter->id}/bloquear");
        $this->actingAs($me)->get("/fotos/{$this->photo->id}")->assertDontSee('comentario pesado');

        $this->actingAs($me)->get('/')->assertSee($this->photo->thumbUrl());
        $this->actingAs($me)->post("/usuarios/{$this->author->id}/bloquear");
        $this->actingAs($me)->get('/')->assertDontSee($this->photo->thumbUrl());
        $this->actingAs($me)->get('/tags/'.Tag::sole()->id)->assertDontSee(route('photos.show', $this->photo));
        $this->actingAs($me)->get("/fotos/{$this->photo->id}")->assertSee('Bloqueaste a quien subió esta foto');
        $this->actingAs($me)->get('/mi-cuenta')->assertSee('molesto')->assertSee('pintor');

        $this->actingAs($me)->post("/usuarios/{$this->author->id}/bloquear");
        $this->actingAs($me)->get('/')->assertSee($this->photo->thumbUrl());

        $this->actingAs($me)->post("/usuarios/{$me->id}/bloquear")->assertStatus(422);
    }
}
