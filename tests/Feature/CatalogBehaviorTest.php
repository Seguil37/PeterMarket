<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class CatalogBehaviorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function por_defecto_ordena_por_nombre_ascendente()
    {
        Product::factory()->create(['name' => 'Zanahoria', 'price' => 5,  'stock' => 10]);
        Product::factory()->create(['name' => 'Manzana',   'price' => 20, 'stock' => 50]);
        Product::factory()->create(['name' => 'Banana',    'price' => 10, 'stock' => 30]);

        $resp = $this->get(route('catalog.index'));

        $resp->assertStatus(200)->assertViewIs('welcome');
        $resp->assertSeeInOrder(['Banana', 'Manzana', 'Zanahoria']);
    }

    /** @test */
    public function puede_ordenar_por_precio_asc_y_desc()
    {
        Product::factory()->create(['name' => 'A', 'price' => 30]);
        Product::factory()->create(['name' => 'B', 'price' => 10]);
        Product::factory()->create(['name' => 'C', 'price' => 20]);

        // asc
        $asc = $this->get(route('catalog.index', ['sort' => 'price_asc']));
        $asc->assertStatus(200)->assertViewIs('welcome');
        $asc->assertSeeInOrder(['B', 'C', 'A']);

        // desc
        $desc = $this->get(route('catalog.index', ['sort' => 'price_desc']));
        $desc->assertStatus(200)->assertViewIs('welcome');
        $desc->assertSeeInOrder(['A', 'C', 'B']);   
    }

    /** @test */
    public function puede_ordenar_por_stock_descendente()
    {
        Product::factory()->create(['name' => 'X', 'stock' => 5]);
        Product::factory()->create(['name' => 'Y', 'stock' => 50]);
        Product::factory()->create(['name' => 'Z', 'stock' => 20]);

        $resp = $this->get(route('catalog.index', ['sort' => 'stock_desc']));

        $resp->assertStatus(200)->assertViewIs('welcome');
        $resp->assertSeeInOrder(['Y', 'Z', 'X']);
    }

    /** @test */
    public function filtra_por_q_en_nombre_con_like_parcial()
    {
        Product::factory()->create(['name' => 'Leche entera']);
        Product::factory()->create(['name' => 'Pan integral']);
        Product::factory()->create(['name' => 'Yogur natural']);

        $resp = $this->get(route('catalog.index', ['q' => 'le']));

        $resp->assertStatus(200)->assertViewIs('welcome');
        $resp->assertSee('Leche entera');
        $resp->assertDontSee('Pan integral');
        $resp->assertDontSee('Yogur natural');
    }

   
}
