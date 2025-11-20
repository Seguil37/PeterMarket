<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutValidationTest extends TestCase
{
    /** @test */
    public function valida_campos_requeridos_y_formato_correcto()
    {
        $response = $this->post(route('checkout.process'), [
            'customer_name'    => '',
            'customer_email'   => 'correo_invalido',
            'shipping_address' => '', // requerido
            'shipping_city'    => '',
            'shipping_reference' => '',
            'shipping_type'    => 'vip', // no permitido
            'payment_method'   => 'bitcoin', // no permitido
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'customer_name',
            'customer_email',
            'shipping_address',
            'shipping_city',
            'shipping_reference',
            'shipping_type',
            'payment_method',
        ]);
    }

    /** @test */
    public function pasa_validacion_con_datos_correctos()
    {
        $response = $this->post(route('checkout.process'), [
            'customer_name'    => 'Cliente Demo',
            'customer_email'   => 'demo@example.com',
            'shipping_address' => 'Av. Siempre Viva 123',
            'shipping_city'    => 'Springfield',
            'shipping_reference' => 'Casa azul, portón negro',
            'shipping_type'    => 'standard',
            'payment_method'   => 'simulated',
        ]);

        $response->assertSessionHasNoErrors();
    }
}
