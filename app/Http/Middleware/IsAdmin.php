<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se o usuário NÃO for admin, redireciona
        if (!auth()->check() || auth()->user()->user_lvl !== 'admin') {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}
