<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TagReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TagReaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_without_a_key_the_reader_is_hidden_and_returns_nothing(): void
    {
        config(['kingtag.reader.api_key' => null]);
        $user = User::factory()->create();

        $this->actingAs($user)->get('/registrar')->assertOk()->assertDontSee('data-tag-reader', false);

        $this->actingAs($user)
            ->post('/registrar/leer-tag', ['photo' => UploadedFile::fake()->image('tag.jpg')])
            ->assertOk()
            ->assertExactJson(['text' => null]);
    }

    public function test_with_a_key_the_page_offers_the_reader_and_returns_its_suggestion(): void
    {
        config(['kingtag.reader.api_key' => 'test']);
        $this->mock(TagReader::class, function ($mock) {
            $mock->shouldReceive('enabled')->andReturn(true);
            $mock->shouldReceive('read')->andReturn('KAOS');
        });
        $user = User::factory()->create();

        $this->actingAs($user)->get('/registrar')->assertOk()->assertSee('data-tag-reader', false);

        $this->actingAs($user)
            ->post('/registrar/leer-tag', ['photo' => UploadedFile::fake()->image('tag.jpg')])
            ->assertOk()
            ->assertExactJson(['text' => 'KAOS']);
    }

    public function test_guests_cannot_use_the_reader(): void
    {
        $this->post('/registrar/leer-tag')->assertRedirect('/entrar');
    }
}
