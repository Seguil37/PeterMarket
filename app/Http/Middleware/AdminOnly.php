<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();   // ✅ Intelephense reconoce Request::user()

        if (!$user || !$user->is_admin) {
            abort(403, 'Solo administradores.');
        }

        return $next($request);
    }
}
