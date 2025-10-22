<?php

namespace Tests\Feature\Models;

use App\Models\Compra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_una_compra_con_totales_coherentes(): void
    {
        $compra = Compra::factory()->create([
            'cantidad' => 4,
            'precio'   => 2.75,
            'total'    => 11.00,
        ]);

        $this->assertNotNull($compra->id);
        $this->assertEquals(11.00, (float) $compra->total);
    }
}
