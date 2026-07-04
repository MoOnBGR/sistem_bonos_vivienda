<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureClienteProfileComplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->tipo_usuario === 'Cliente' && !$user->cliente) {
            if (!$request->routeIs('cliente.datos', 'cliente.store', 'logout')) {
                return redirect()->route('cliente.datos');
            }
        }

        return $next($request);
    }
}