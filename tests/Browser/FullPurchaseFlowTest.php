<?php

namespace Tests\Browser;

use App\Models\Product;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FullPurchaseFlowTest extends DuskTestCase
{
    public function testFlujoCompleto()
    {
        $productName = 'Producto Dusk ' . uniqid();

        $this->browse(function (Browser $browser) use ($productName) {
            
            // ===== PASO 1: LOGIN ADMIN =====
            $browser->visit('/admin/login')
                ->pause(1000)
                ->type('email', 'admin@tuapp.com')
                ->type('password', 'clave-super-segura')
                ->press('Entrar al panel')
                ->pause(3000)
                ->screenshot('01-admin-login');
            
            // Verificar que entró al admin
            $this->assertStringContainsString('/admin', $browser->driver->getCurrentURL());

            // ===== PASO 2: IR A PRODUCTOS =====
            $browser->visit('/admin/products')
                ->pause(2000)
                ->screenshot('02-lista-productos');

            // ===== PASO 3: CREAR NUEVO PRODUCTO =====
            $browser->visit('/admin/products/create')
                ->pause(2000)
                ->type('name', $productName)
                ->select('category_type', 'Abarrotes')
                ->type('description', 'Producto creado en la prueba de sistema.')
                ->type('price', '9.99')
                ->type('stock', '5')
                ->type('image_url', 'https://picsum.photos/seed/dusk-product/300/300')
                ->screenshot('03-formulario-producto')
                ->press('Cargar imagen')
                ->pause(3000)
                ->screenshot('04-producto-creado');

            // ===== PASO 4: SALIR DEL ADMIN =====
            $browser->visit('/admin/logout')
                ->pause(2000);

            // ===== PASO 5: OBTENER PRODUCTO DE LA BD =====
            $product = Product::where('name', $productName)
                ->latest('id')
                ->firstOrFail();

            echo "\n✓ Producto creado: {$product->name} (ID: {$product->id})\n";

            // ===== PASO 6: BUSCAR PRODUCTO EN LA TIENDA =====
            $browser->visit('/')
                ->pause(2000)
                ->screenshot('05-home');
            
            // Buscar el producto
            $browser->type('q', $productName)
                ->press('Aplicar')
                ->pause(2000)
                ->screenshot('06-busqueda-producto');

            // ===== PASO 7: VER DETALLE DEL PRODUCTO =====
            $productUrl = route('catalog.show', $product);
            echo "\n✓ Visitando producto: $productUrl\n";
            
            $browser->visit($productUrl)
                ->pause(2000)
                ->screenshot('07-detalle-producto');

            // ===== PASO 8: AÑADIR AL CARRITO =====
            $browser->clear('input[name="quantity"]')
                ->type('quantity', '2')
                ->screenshot('08-antes-agregar-carrito')
                ->press('Añadir al carrito')
                ->pause(3000)
                ->screenshot('09-despues-agregar-carrito');

            // ===== PASO 9: IR AL CARRITO SI NO ESTÁ AHÍ =====
            $currentUrl = $browser->driver->getCurrentURL();
            echo "\n✓ URL después de agregar al carrito: $currentUrl\n";
            
            if (!str_contains($currentUrl, '/cart')) {
                $browser->visit('/cart')->pause(2000);
            }
            
            $browser->screenshot('10-carrito');

            // ===== PASO 10: LLENAR FORMULARIO DE CHECKOUT =====
            $browser->select('delivery_type', 'pickup')
                ->type('customer_name', 'Cliente Dusk')
                ->type('customer_email', 'cliente+dusk@example.com')
                ->screenshot('11-formulario-checkout')
                ->press('Pagar ahora')
                ->pause(5000)
                ->screenshot('12-orden-final');
            
            // ===== PASO 11: VERIFICAR ÉXITO =====
            $finalUrl = $browser->driver->getCurrentURL();
            echo "\n✓ URL final: $finalUrl\n";
            
            // Verificar que llegó a alguna página de confirmación
            $this->assertTrue(
                str_contains($finalUrl, 'success') || 
                str_contains($finalUrl, 'order') ||
                str_contains($finalUrl, 'thank') ||
                str_contains($finalUrl, 'gracias'),
                "La URL final no indica éxito: $finalUrl"
            );
            
            echo "\n✅ ¡PRUEBA COMPLETA EXITOSA!\n";
        });
    }
}