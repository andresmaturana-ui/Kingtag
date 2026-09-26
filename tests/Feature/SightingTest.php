<?php

namespace Tests\Feature;

use App\Models\Graffiti;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SightingTest extends TestCase
{
    use RefreshDatabase;

    private const LAT = -33.4489;

    private const LNG = -70.6693;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function register(User $user, string $text, float $lat = self::LAT, float $lng = self::LNG, ?int $accuracy = null)
    {
        return $this->actingAs($user)->post('/registrar', array_filter([
            'text' => $text,
            'lat' => $lat,
            'lng' => $lng,
            'accuracy' => $accuracy,
            'photo' => UploadedFile::fake()->image('tag.jpg', 1200, 900),
        ], fn ($v) => $v !== null));
    }

    public function test_registering_a_new_tag_creates_the_tag_the_graffiti_and_the_photo(): void
    {
        $user = User::factory()->create();

        $this->register($user, 'Kase')->assertRedirect()->assertSessionHas('status');

        $tag = Tag::sole();
        $this->assertSame('KASE', $tag->normalized);
        $this->assertFalse($tag->isClaimed());

        $graffiti = Graffiti::sole();
        $this->assertSame(1, $graffiti->reports);
        $this->assertCount(1, $graffiti->photos);
        Storage::disk('public')->assertExists([$graffiti->photo, $graffiti->thumb]);
    }

    public function test_the_same_tag_in_the_same_place_is_registered_only_once(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->register($a, 'KASE');
        // Unos 11 metros más allá, escrito distinto.
        $this->register($b, 'kase', self::LAT + 0.0001)
            ->assertSessionHas('status', 'Este grafiti ya estaba registrado. Sumamos tu foto.');

        $graffiti = Graffiti::sole();
        $this->assertSame(2, $graffiti->reports);
        $this->assertCount(2, $graffiti->photos);
    }

    public function test_an_imprecise_location_looks_for_the_same_graffiti_a_bit_farther(): void
    {
        $user = User::factory()->create();

        $this->register($user, 'KASE');
        // Unos 33 metros más allá, pero el teléfono dijo que su precisión era de 40 m.
        $this->register($user, 'KASE', self::LAT + 0.0003, accuracy: 40);

        $this->assertSame(1, Graffiti::count());
    }

    public function test_the_merge_radius_never_grows_past_the_maximum(): void
    {
        $user = User::factory()->create();

        $this->register($user, 'KASE');
        // Unos 55 metros más allá, con una lectura muy mala.
        $this->register($user, 'KASE', self::LAT + 0.0005, accuracy: 500);

        $this->assertSame(2, Graffiti::count());
    }

    public function test_the_same_tag_far_away_is_a_new_graffiti(): void
    {
        $user = User::factory()->create();

        $this->register($user, 'KASE');
        // Unos 55 metros más allá.
        $this->register($user, 'KASE', self::LAT + 0.0005);

        $this->assertSame(1, Tag::count());
        $this->assertSame(2, Graffiti::count());
    }

    public function test_different_tags_in_the_same_place_are_different_graffitis(): void
    {
        $user = User::factory()->create();

        $this->register($user, 'KASE');
        $this->register($user, 'ROMA');

        $this->assertSame(2, Tag::count());
        $this->assertSame(2, Graffiti::count());
    }

    public function test_location_and_photo_are_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/registrar', ['text' => 'KASE'])
            ->assertSessionHasErrors(['lat', 'lng', 'photo']);

        $this->assertSame(0, Graffiti::count());
    }

    public function test_tag_text_needs_letters_or_numbers(): void
    {
        $this->register(User::factory()->create(), '***')->assertSessionHasErrors('text');

        $this->assertSame(0, Tag::count());
    }
}
