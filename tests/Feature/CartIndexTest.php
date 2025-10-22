<?php

namespace Tests\Feature;

use Tests\TestCase;

class CartIndexTest extends TestCase
{
    

    /** @test */
    public function muestra_vista_aun_si_el_carrito_esta_vacio()
    {
        $resp = $this->get(route('cart.index'));

        $resp->assertStatus(200)
             ->assertViewIs('cart.index')
             ->assertViewHas('cart', [])
             ->assertViewHas('subtotal', 0.0)
             ->assertViewHas('iva', 0.0)
             ->assertViewHas('total', 0.0);
    }
}
