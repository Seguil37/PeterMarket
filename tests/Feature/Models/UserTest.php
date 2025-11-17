<?php

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_crear_usuario_con_factory(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertIsString($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_usuario_tiene_muchos_pedidos(): void
    {
        $user = User::factory()->create();

        $o1 = Order::factory()->for($user)->create();
        $o2 = Order::factory()->for($user)->create();

        $user->load('orders');

        $this->assertCount(2, $user->orders);
        $this->assertEquals($user->id, $o1->user_id);
        $this->assertEquals($user->id, $o2->user_id);
    }

    public function test_password_se_guarda_hasheado(): void
    {
        $plain = 'secret123!';
        $user  = User::factory()->create(['password' => $plain]);

        // No debe quedar igual en BD:
        $this->assertNotEquals($plain, $user->getRawOriginal('password'));
        // Debe validar con Hash:
        $this->assertTrue(Hash::check($plain, $user->password));
    }

    public function test_email_es_unico_en_bd(): void
    {
        $email = 'unique@test.com';
        User::factory()->create(['email' => $email]);

        $this->expectException(QueryException::class); // índice único de la migración users
        User::factory()->create(['email' => $email]);   // debería lanzar excepción
    }

    public function test_is_admin_funciona_como_booleano(): void
    {
        $admin = User::factory()->create(['is_admin' => 1, 'admin_role' => User::ROLE_OPERATOR]);
        $user  = User::factory()->create(['is_admin' => 0, 'admin_role' => null]);

        $this->assertTrue((bool) $admin->is_admin);
        $this->assertFalse((bool) $user->is_admin);
    }

    public function test_detecta_roles_de_admin_master_y_usuario(): void
    {
        $master = User::factory()->admin(User::ROLE_MASTER)->create();
        $operator = User::factory()->admin(User::ROLE_OPERATOR)->create();

        $this->assertTrue($master->isMasterAdmin());
        $this->assertFalse($master->isOperatorAdmin());

        $this->assertTrue($operator->isOperatorAdmin());
        $this->assertFalse($operator->isMasterAdmin());
    }
}
