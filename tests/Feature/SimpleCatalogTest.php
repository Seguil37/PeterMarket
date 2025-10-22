<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class SimpleCatalogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function la_pagina_principal_muestra_los_productos()
    {
        // 1️⃣ Arrange - Creamos 3 productos en la BD
        $products = Product::factory()->count(3)->create();

        // 2️⃣ Act - Hacemos GET a la ruta del catálogo
        $response = $this->get('/');

        // 3️⃣ Assert - Verificamos que todo esté correcto
        $response->assertStatus(200);               // La página carga correctamente
        $response->assertViewIs('welcome');         // Usa la vista correcta

        // Comprobamos que los nombres de los productos aparecen en el HTML
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }
}
