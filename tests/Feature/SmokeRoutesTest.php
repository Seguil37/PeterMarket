<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeRoutesTest extends TestCase
{
    /** @test */
    public function pagina_nosotros_responde_200()
    {
        $this->get(route('about'))->assertStatus(200);
    }

    /** @test */
    public function pagina_login_responde_200()
    {
        $this->get(route('login'))->assertStatus(200);
    }
}
