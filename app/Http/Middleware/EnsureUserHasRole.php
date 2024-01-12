<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check() || !$request->user()->hasAnyRole($roles)) {
            // Si el usuario no está autenticado o no tiene ninguno de los roles, redirige a la ruta 'prohibido'
            return redirect()->route('prohibido');
        }

        return $next($request);
    }
}
