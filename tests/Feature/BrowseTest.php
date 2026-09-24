<?php

namespace Tests\Feature;

use App\Models\Graffiti;
use App\Models\Photo;
use App\Models\Tag;
use App\Services\Ranking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrowseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crea tags con la cantidad de grafitis indicada, en ese orden.
     *
     * @return array<string, Tag>
     */
    private function tagsWithGraffitis(array $counts): array
    {
        $tags = [];
        foreach ($counts as $text => $count) {
            $tags[$text] = Tag::factory()->create(['text' => $text, 'normalized' => $text]);
            Graffiti::factory()->count($count)->for($tags[$text])->create();
        }

        return $tags;
    }

    public function test_pages_load(): void
    {
        $this->get('/')->assertOk()->assertSee('Ingresa tu tag')->assertSee('Registrar tag')->assertSee('Buscar tag');
        $this->get('/buscar')->assertOk();
        $this->get('/ranking')->assertOk();
        $this->get('/entrar')->assertOk();
        $this->get('/registro')->assertOk();
    }

    public function test_home_shows_the_latest_photos_newest_first(): void
    {
        $this->get('/')->assertSee('Todavía no hay grafitis');

        $tags = $this->tagsWithGraffitis(['VIEJO' => 1, 'NUEVO' => 1]);
        foreach ($tags as $tag) {
            $graffiti = $tag->graffitis()->sole();
            Photo::create(['graffiti_id' => $graffiti->id, 'path' => $graffiti->photo, 'thumb' => $graffiti->thumb]);
        }

        $this->get('/')->assertOk()
            ->assertSeeInOrder(['NUEVO', 'VIEJO'])
            ->assertSee(route('tags.show', $tags['NUEVO']))
            ->assertDontSee('Ver más');
    }

    public function test_ranking_orders_tags_by_number_of_graffitis(): void
    {
        $this->tagsWithGraffitis(['ROMA' => 1, 'KASE' => 3, 'NEKO' => 2]);
        Tag::factory()->create(['text' => 'VACIO', 'normalized' => 'VACIO']);

        $this->assertSame(['KASE', 'NEKO', 'ROMA'], app(Ranking::class)->top()->pluck('text')->all());
        $this->get('/ranking')->assertOk()->assertSeeInOrder(['KASE', 'NEKO', 'ROMA'])->assertDontSee('VACIO');
    }

    public function test_ranking_shows_top_20_only(): void
    {
        $counts = [];
        foreach (range(1, 25) as $i) {
            $counts['TAG'.$i] = 1;
        }
        $this->tagsWithGraffitis($counts);

        $this->assertCount(20, app(Ranking::class)->top());
    }

    public function test_profile_shows_position_and_neighbours(): void
    {
        $tags = $this->tagsWithGraffitis(['KASE' => 3, 'NEKO' => 2, 'ROMA' => 1]);

        $around = app(Ranking::class)->around($tags['NEKO']);
        $this->assertSame(2, $around['position']);
        $this->assertSame('KASE', $around['above']->text);
        $this->assertSame('ROMA', $around['below']->text);

        $this->get('/tags/'.$tags['NEKO']->id)->assertOk()->assertSee('#2')->assertSeeInOrder(['KASE', 'NEKO', 'ROMA']);
    }

    public function test_search_by_text(): void
    {
        $this->tagsWithGraffitis(['KASE' => 1, 'KASEONE' => 1, 'ROMA' => 1]);

        $this->get('/buscar?q=kase')->assertOk()->assertSee('KASEONE')->assertDontSee('ROMA');
    }

    public function test_nearby_returns_graffitis_within_100_meters(): void
    {
        $tag = Tag::factory()->create();
        Graffiti::factory()->for($tag)->create(['lat' => -33.4489, 'lng' => -70.6693]);
        Graffiti::factory()->for($tag)->create(['lat' => -33.4496, 'lng' => -70.6693]); // ~78 m
        Graffiti::factory()->for($tag)->create(['lat' => -33.4520, 'lng' => -70.6693]); // ~345 m

        $this->getJson('/mapa/cerca?lat=-33.4489&lng=-70.6693')
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.distance', 0)
            ->assertJsonPath('1.distance', 78);
    }

    public function test_map_returns_graffitis_inside_the_visible_area(): void
    {
        $tag = Tag::factory()->create();
        Graffiti::factory()->for($tag)->create(['lat' => -33.45, 'lng' => -70.67]);
        Graffiti::factory()->for($tag)->create(['lat' => -33.60, 'lng' => -70.67]);

        $this->getJson('/mapa/grafitis?south=-33.5&north=-33.4&west=-70.7&east=-70.6')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.tag', $tag->text);
    }
}
