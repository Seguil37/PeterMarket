<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutesExistTest extends TestCase
{
    /** @test */
    public function rutas_publicas_estan_registradas()
    {
        $this->assertTrue(Route::has('catalog.index'));
        $this->assertTrue(Route::has('cart.index'));
        $this->assertTrue(Route::has('cart.add'));
        $this->assertTrue(Route::has('cart.update'));
        $this->assertTrue(Route::has('cart.remove'));
        $this->assertTrue(Route::has('cart.clear'));
        $this->assertTrue(Route::has('checkout.process'));
        $this->assertTrue(Route::has('order.success'));
        $this->assertTrue(Route::has('about'));
        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('login.post'));
        $this->assertTrue(Route::has('logout'));
    }

    /** @test */
    public function rutas_admin_estan_registradas()
    {
        $this->assertTrue(Route::has('admin.dashboard'));
        $this->assertTrue(Route::has('admin.inventory.index'));
        $this->assertTrue(Route::has('admin.inventory.store'));
        $this->assertTrue(Route::has('admin.inventory.destroy'));
        $this->assertTrue(Route::has('admin.products.index')); // del resource
    }
}
