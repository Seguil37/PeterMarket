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
        $user = Auth::user();

        if (!$user || !$user->is_admin || !$user->is_master_admin || !$user->is_active) {
            abort(403, 'Acceso restringido al Admin Master.');
        }

        return $next($request);
    }
}
