<?php

namespace Tests\Feature;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_registrar_un_movimiento_de_entrada(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->actingAs($admin)
            ->from(route('admin.inventory.index'))
            ->post(route('admin.inventory.store'), [
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => 4,
                'unit_cost' => 9.50,
                'note' => 'Reposición semanal',
            ]);

        $response->assertRedirect(route('admin.inventory.index'))
            ->assertSessionHas('ok');

        $movement = InventoryMovement::first();

        $this->assertNotNull($movement);
        $this->assertSame($admin->id, $movement->user_id);
        $this->assertSame('in', $movement->type);
        $this->assertSame(4, $movement->quantity);
        $this->assertSame('Reposición semanal', $movement->note);

        $this->assertSame(9, $product->fresh()->stock);
    }

    public function test_eliminar_movimiento_revierte_el_stock(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $product = Product::factory()->create(['stock' => 12]);

        $movement = InventoryMovement::create([
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 3,
            'unit_cost' => null,
            'note' => 'Rotura de empaque',
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.inventory.index'))
            ->delete(route('admin.inventory.destroy', $movement));

        $response->assertRedirect(route('admin.inventory.index'))
            ->assertSessionHas('ok');

        $this->assertDatabaseMissing('inventory_movements', ['id' => $movement->id]);
        $this->assertSame(15, $product->fresh()->stock);
    }
}
