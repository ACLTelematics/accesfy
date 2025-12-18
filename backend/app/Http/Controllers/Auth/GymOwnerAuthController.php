<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GymOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GymOwnerAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $gymOwner = GymOwner::where('username', $request->username)->first();

        if (!$gymOwner || !Hash::check($request->password, $gymOwner->password)) {
            throw ValidationException::withMessages([
                'username' => ['Las credenciales son incorrectas.'],
            ]);
        }

        if (!$gymOwner->active) {
            throw ValidationException::withMessages([
                'username' => ['Tu cuenta está inactiva.'],
            ]);
        }

        if ($gymOwner->activated_until && $gymOwner->activated_until < now()) {
            throw ValidationException::withMessages([
                'username' => ['Tu cuenta ha expirado.'],
            ]);
        }

        $token = $gymOwner->createToken('gym-owner-token')->plainTextToken;

        return response()->json([
            'user' => $gymOwner,
            'token' => $token,
            'token_type' => 'Bearer',
            'role' => 'gym_owner'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente',
        ]);
    }

    public function me(Request $request)
    {
        $gymOwner = $request->user();
        $gymOwner->load('staff', 'clients', 'memberships');

        return response()->json($gymOwner);
    }
}
