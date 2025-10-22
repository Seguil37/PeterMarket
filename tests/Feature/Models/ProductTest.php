<?php

namespace Tests\Feature\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_productos_validos_con_factory(): void
    {
        $product = Product::factory()->create();

        $this->assertNotNull($product->id);
        $this->assertIsString($product->name);
        $this->assertIsFloat((float) $product->price);
        $this->assertIsInt((int) $product->stock);
    }

    public function test_puede_listar_y_filtrar_por_nombre(): void
    {
        Product::factory()->count(3)->create(['name' => 'Manzana Roja']);
        Product::factory()->count(2)->create(['name' => 'Banana']);

        $found = Product::where('name', 'like', '%Manzana%')->get();

        $this->assertCount(3, $found);
    }
}
