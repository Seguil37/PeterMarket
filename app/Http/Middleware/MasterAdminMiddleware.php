<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MasterAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->is_admin || !Auth::user()->is_master_admin) {
            abort(403, 'Solo el Admin Master puede acceder a este módulo.');
        }

        return $next($request);
    }
}
