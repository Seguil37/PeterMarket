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
            'customer_address' => 123, // no es string
            'payment_method'   => 'bitcoin', // no permitido
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'customer_name',
            'customer_email',
            'customer_address',
            'payment_method',
        ]);
    }

    /** @test */
    public function pasa_validacion_con_datos_correctos()
    {
        $response = $this->post(route('checkout.process'), [
            'customer_name'    => 'Cliente Demo',
            'customer_email'   => 'demo@example.com',
            'customer_address' => 'Av. Siempre Viva 123',
            'payment_method'   => 'simulated',
        ]);

        $response->assertSessionHasNoErrors();
    }
}
