<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario autenticado sea un administrador
        if (!$request->user() || !($request->user() instanceof \App\Models\Administrador)) {
            return response()->json([
                'message' => 'No autorizado. Se requieren permisos de administrador.'
            ], 403);
        }

        // Verificar que el administrador esté activo
        if (!$request->user()->activo) {
            return response()->json([
                'message' => 'Cuenta de administrador inactiva.'
            ], 403);
        }

        return $next($request);
    }
}
