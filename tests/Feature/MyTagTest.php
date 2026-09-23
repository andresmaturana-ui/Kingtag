<?php

namespace Tests\Feature;

use App\Models\Graffiti;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MyTagTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function claim(User $user, string $text)
    {
        return $this->actingAs($user)->post('/mi-tag', [
            'text' => $text,
            'photo' => UploadedFile::fake()->image('yo.jpg', 800, 800),
        ]);
    }

    public function test_an_artist_can_claim_a_new_tag(): void
    {
        $user = User::factory()->create();

        $this->claim($user, 'Kase')->assertRedirect();

        $tag = Tag::sole();
        $this->assertSame($user->id, $tag->artist_id);
        $this->assertSame('KASE', $tag->text);
        Storage::disk('public')->assertExists($tag->profile_photo);
    }

    public function test_claiming_a_tag_that_others_registered_brings_its_graffitis(): void
    {
        $tag = Tag::factory()->create(['text' => 'KASE', 'normalized' => 'KASE']);
        Graffiti::factory()->count(3)->for($tag)->create();
        $user = User::factory()->create();

        $this->claim($user, 'kase');

        $this->assertSame($user->id, $tag->fresh()->artist_id);
        $this->assertSame(3, $user->fresh()->tag->graffitis()->count());
    }

    public function test_a_tag_claimed_by_someone_else_cannot_be_taken(): void
    {
        $owner = User::factory()->create();
        Tag::factory()->create(['text' => 'KASE', 'normalized' => 'KASE', 'artist_id' => $owner->id]);

        $this->claim(User::factory()->create(), 'KASE')->assertSessionHasErrors('text');

        $this->assertSame($owner->id, Tag::sole()->artist_id);
    }

    public function test_an_account_has_one_tag(): void
    {
        $user = User::factory()->create();
        $this->claim($user, 'KASE');

        $this->claim($user, 'ROMA')->assertSessionHasErrors('text');

        $this->assertSame(1, Tag::count());
    }

    public function test_my_profile_goes_to_my_tag_or_to_claim_one(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/mi-perfil')->assertRedirect('/mi-tag');

        $this->claim($user, 'KASE');
        $this->actingAs($user)->get('/mi-perfil')->assertRedirect('/tags/'.Tag::sole()->id);
    }
}
