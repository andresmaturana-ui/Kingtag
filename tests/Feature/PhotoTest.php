<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    use RefreshDatabase;

    private Photo $photo;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(User::factory()->create())->post('/registrar', [
            'text' => 'KASE',
            'lat' => -33.4489,
            'lng' => -70.6693,
            'photo' => UploadedFile::fake()->image('tag.jpg', 800, 600),
        ]);
        auth()->logout();
        $this->photo = Photo::sole();
    }

    public function test_guests_see_the_photo_but_must_log_in_to_like_or_comment(): void
    {
        $this->get("/fotos/{$this->photo->id}")->assertOk()->assertSee('0 me gusta')->assertSee('para comentar');

        $this->post("/fotos/{$this->photo->id}/me-gusta")->assertRedirect('/entrar');
        $this->post("/fotos/{$this->photo->id}/comentarios", ['body' => 'hola'])->assertRedirect('/entrar');
    }

    public function test_like_toggles_and_counts_once_per_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post("/fotos/{$this->photo->id}/me-gusta")->assertRedirect("/fotos/{$this->photo->id}");
        $this->actingAs(User::factory()->create())->post("/fotos/{$this->photo->id}/me-gusta");
        $this->assertSame(2, $this->photo->likers()->count());
        $this->actingAs($user)->get("/fotos/{$this->photo->id}")->assertSee('2 me gusta')->assertSee('aria-pressed="true"', false);
        $this->get('/')->assertSee('KASE · ♥ 2');

        $this->actingAs($user)->post("/fotos/{$this->photo->id}/me-gusta");
        $this->assertSame(1, $this->photo->likers()->count());
    }

    public function test_comments_can_be_written_and_deleted_by_their_author_or_an_admin(): void
    {
        $author = User::factory()->create(['username' => 'ana']);

        $this->actingAs($author)->post("/fotos/{$this->photo->id}/comentarios", ['body' => ''])->assertSessionHasErrors('body');
        $this->actingAs($author)->post("/fotos/{$this->photo->id}/comentarios", ['body' => 'Buena pieza'])->assertRedirect();
        $this->get("/fotos/{$this->photo->id}")->assertSee('ana')->assertSee('Buena pieza');

        $comment = Comment::sole();
        $this->actingAs(User::factory()->create())->delete("/comentarios/{$comment->id}")->assertForbidden();

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->delete("/comentarios/{$comment->id}")->assertRedirect();
        $this->assertModelMissing($comment);

        $this->actingAs($author)->post("/fotos/{$this->photo->id}/comentarios", ['body' => 'Otra']);
        $this->actingAs($author)->delete('/comentarios/'.Comment::sole()->id)->assertRedirect();
        $this->assertSame(0, Comment::count());
    }

    public function test_deleting_a_photo_removes_its_likes_and_comments(): void
    {
        $user = User::factory()->create();
        $this->photo->likers()->attach($user);
        $this->photo->comments()->create(['user_id' => $user->id, 'body' => 'hola']);

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->delete("/fotos/{$this->photo->id}");

        $this->assertSame(0, Comment::count());
        $this->assertDatabaseCount('photo_likes', 0);
    }
}
