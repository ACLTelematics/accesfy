<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Filtrar por gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\SuperUser) {
            // SuperUser ve TODO el staff de todos los gyms
            $staff = Staff::with('gymOwner')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user instanceof \App\Models\GymOwner) {
            // GymOwner solo ve su propio staff
            $staff = Staff::where('gym_owner_id', $user->id)
                ->with('gymOwner')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user instanceof \App\Models\Staff) {
            // Staff puede ver a sus compañeros del mismo gym
            $staff = Staff::where('gym_owner_id', $user->gym_owner_id)
                ->with('gymOwner')
                ->orderBy('created_at', 'desc')
                ->get();

        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($staff);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gym_owner_id' => 'required|exists:gym_owners,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff',
            'username' => 'required|string|unique:staff',
            'password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        // Validar que solo pueda crear staff para su propio gym
        if ($user instanceof \App\Models\GymOwner) {
            if ($validated['gym_owner_id'] != $user->id) {
                return response()->json(['error' => 'No puedes crear staff para otro gimnasio'], 403);
            }
        } elseif ($user instanceof \App\Models\Staff) {
            return response()->json(['error' => 'Staff no puede crear otros staff'], 403);
        }
        // SuperUser puede crear para cualquier gym

        $validated['password'] = Hash::make($validated['password']);
        $validated['active'] = true;

        $staff = Staff::create($validated);

        return response()->json($staff->load('gymOwner'), 201);
    }

    public function show(Request $request, Staff $staff)
    {
        $user = $request->user();

        // Validar que solo pueda ver staff de su gym
        if ($user instanceof \App\Models\GymOwner) {
            if ($staff->gym_owner_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($user instanceof \App\Models\Staff) {
            if ($staff->gym_owner_id !== $user->gym_owner_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        // SuperUser puede ver cualquier staff

        $staff->load('gymOwner', 'attendances', 'payments');
        return response()->json($staff);
    }

    public function update(Request $request, Staff $staff)
    {
        $user = $request->user();

        // Validar que solo pueda actualizar staff de su gym
        if ($user instanceof \App\Models\GymOwner) {
            if ($staff->gym_owner_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($user instanceof \App\Models\Staff) {
            return response()->json(['error' => 'Staff no puede actualizar otros staff'], 403);
        }
        // SuperUser puede actualizar cualquier staff

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:staff,email,' . $staff->id,
            'username' => 'sometimes|string|unique:staff,username,' . $staff->id,
            'password' => 'sometimes|string|min:6',
            'active' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $staff->update($validated);

        return response()->json($staff->load('gymOwner'));
    }

    public function destroy(Request $request, Staff $staff)
    {
        $user = $request->user();

        // Validar que solo pueda eliminar staff de su gym
        if ($user instanceof \App\Models\GymOwner) {
            if ($staff->gym_owner_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($user instanceof \App\Models\Staff) {
            return response()->json(['error' => 'Staff no puede eliminar otros staff'], 403);
        }
        // SuperUser puede eliminar cualquier staff

        $staff->delete();

        return response()->json(null, 204);
    }

    /**
     * Obtener staff del gym_owner actual
     * Usado por el frontend en StaffListView
     */
    public function getByGymOwner(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } elseif ($user instanceof \App\Models\SuperUser) {
            // SuperUser ve todo el staff de todos los gyms
            return response()->json(
                Staff::with('gymOwner')
                    ->orderBy('created_at', 'desc')
                    ->get()
            );
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $staff = Staff::where('gym_owner_id', $gymOwnerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($staff);
    }
/**
 * Cambiar contraseña del staff actual
 */
public function changePassword(Request $request)
{
    $user = $request->user();

    $validated = $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:6|confirmed',
    ]);

    // Verificar que la contraseña actual sea correcta
    if (!Hash::check($validated['current_password'], $user->password)) {
        return response()->json([
            'message' => 'La contraseña actual es incorrecta'
        ], 400);
    }

    // Actualizar contraseña
    $user->update([
        'password' => Hash::make($validated['new_password'])
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Contraseña actualizada exitosamente'
    ]);
}
    /**
     * Resetear contraseña de un staff member
     */
    public function resetPassword(Request $request, Staff $staff)
    {
        $user = $request->user();

        // Validar permisos
        if ($user instanceof \App\Models\GymOwner) {
            if ($staff->gym_owner_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($user instanceof \App\Models\Staff) {
            return response()->json(['error' => 'Staff no puede resetear contraseñas'], 403);
        }
        // SuperUser puede resetear cualquier contraseña

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $staff->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }
}
