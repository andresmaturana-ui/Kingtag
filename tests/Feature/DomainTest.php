<?php

namespace Tests\Feature;

use Tests\TestCase;

class DomainTest extends TestCase
{
    public function test_tagking_cl_lleva_a_la_misma_pagina_en_tagking_org(): void
    {
        config(['app.url' => 'https://tagking.org']);

        $this->get('https://tagking.cl/ranking?x=1')
            ->assertStatus(301)
            ->assertRedirect('https://tagking.org/ranking?x=1');

        $this->get('https://www.tagking.org/buscar')
            ->assertRedirect('https://tagking.org/buscar');
    }

    public function test_la_direccion_oficial_no_se_redirige(): void
    {
        config(['app.url' => 'https://tagking.org']);

        $this->get('https://tagking.org/ayuda')->assertOk();
    }
}
