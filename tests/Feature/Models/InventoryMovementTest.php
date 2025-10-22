<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryMovementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crearse_por_asignacion_masiva_y_se_persiste_en_bd()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $movement = InventoryMovement::create([
            'product_id' => $product->id,
            'type'       => 'in',       // o el valor que uses: 'in'/'out'
            'quantity'   => 5,
            'unit_cost'  => 12.50,
            'note'       => 'Ingreso inicial',
            'user_id'    => $user->id,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'id'         => $movement->id,
            'product_id' => $product->id,
            'type'       => 'in',
            'quantity'   => 5,
            'unit_cost'  => 12.50,
            'note'       => 'Ingreso inicial',
            'user_id'    => $user->id,
        ]);
    }

    /** @test */
    public function relacion_product_funciona()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $movement = InventoryMovement::create([
            'product_id' => $product->id,
            'type'       => 'in',
            'quantity'   => 1,
            'unit_cost'  => 1,
            'note'       => null,
            'user_id'    => $user->id,
        ]);

        $this->assertTrue($movement->product->is($product));
    }

    /** @test */
    public function relacion_user_funciona()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $movement = InventoryMovement::create([
            'product_id' => $product->id,
            'type'       => 'out',
            'quantity'   => 2,
            'unit_cost'  => 0,
            'note'       => 'Salida por prueba',
            'user_id'    => $user->id,
        ]);

        $this->assertTrue($movement->user->is($user));
    }

    /** @test */
    public function ignora_campos_no_fillable_en_asignacion_masiva()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $movement = InventoryMovement::create([
            'product_id' => $product->id,
            'type'       => 'in',
            'quantity'   => 3,
            'unit_cost'  => 5.75,
            'note'       => null,
            'user_id'    => $user->id,
            'no_existe'  => 'debe_ignorarse', // NO está en $fillable
        ]);

        $this->assertArrayNotHasKey('no_existe', $movement->getAttributes());
    }
}
