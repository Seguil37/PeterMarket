<?php

namespace Tests\Browser;

use App\Models\Product;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FlujoCompletoTest extends DuskTestCase
{
    public function test_flujo_completo()
    {
        $this->artisan('migrate:fresh', ['--seed' => true]);

        $productName = 'Producto Dusk '.now()->format('His');

        $this->browse(function (Browser $browser) use ($productName) {
            // Login de administrador
            $browser->visit(route('admin.login'))
                ->assertSee('Iniciar sesión (Admin)')
                ->type('email', 'admin@tuapp.com')
                ->type('password', 'clave-super-segura')
                ->check('remember')
                ->press('Entrar al panel')
                ->assertPathIs('/admin')
                ->assertSee('Control central de Peter Market')
                ->screenshot('admin-dashboard');

            // Crear un producto nuevo desde el panel
            $browser->clickLink('Productos')
                ->assertSee('Productos publicados')
                ->clickLink('Nuevo producto')
                ->type('name', $productName)
                ->select('category_type', 'Abarrotes')
                ->type('description', 'Producto creado por prueba de navegador')
                ->type('price', '20.50')
                ->type('stock', '50')
                ->type('image_url', 'https://picsum.photos/seed/dusk/300/300')
                ->press('Cargar imagen')
                ->assertPathIs('/admin/products')
                ->assertSee('Producto creado.')
                ->assertSee($productName)
                ->screenshot('producto-creado');

            $productId = Product::where('name', $productName)->value('id');

            // Visitar la ficha del producto en el catálogo público
            $browser->visit(route('catalog.show', $productId))
                ->assertSee($productName)
                ->type('quantity', '2')
                ->press('Añadir al carrito')
                ->assertPathIs('/cart')
                ->assertSee('Producto agregado al carrito.')
                ->assertSee($productName)
                ->screenshot('carrito-actualizado');

            // Completar el checkout simulando delivery
            $browser->type('customer_name', 'Cliente Dusk')
                ->type('customer_email', 'cliente@example.com')
                ->select('delivery_type', 'delivery')
                ->type('shipping_address', 'Av. Siempre Viva 742')
                ->type('shipping_city', 'Cusco')
                ->type('shipping_reference', 'Frente a la plaza')
                ->select('shipping_type', 'standard')
                ->radio('payment_method', 'simulated')
                ->press('Pagar ahora')
                ->assertPathBeginsWith('/order/success')
                ->assertSee('¡Gracias por tu compra!')
                ->assertSee($productName)
                ->screenshot('checkout-exitoso');
        });
    }
}
