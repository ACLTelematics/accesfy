<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\GymOwner;

class CheckGymOwnerExpiration
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Solo verificar si es GymOwner
        if ($user instanceof GymOwner) {
            // Verificar si está expirado
            if ($user->activated_until && $user->activated_until < now()) {
                return response()->json([
                    'message' => 'Tu cuenta ha expirado. Contacta al administrador.',
                    'error' => 'Account Expired'
                ], 403);
            }

            // Verificar si está inactivo
            if (!$user->active) {
                return response()->json([
                    'message' => 'Tu cuenta está inactiva.',
                    'error' => 'Account Inactive'
                ], 403);
            }
        }

        return $next($request);
    }
}