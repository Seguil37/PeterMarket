<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * Puedes dejarlo vacío; usaremos tokensMatch con cabecera.
     */
    protected $except = [
        // 'cart', 'cart/*'  // <-- evita excluir globalmente
    ];

    /**
     * Override para permitir tráfico de Cypress en local/testing
     */
    protected function tokensMatch($request)
    {
        // Si estamos en local o testing y viene cabecera X-CYPRESS: 1, saltamos CSRF
        if (app()->environment(['local', 'testing']) && $request->header('X-CYPRESS') === '1') {
            return true;
        }

        return parent::tokensMatch($request);
    }
}
