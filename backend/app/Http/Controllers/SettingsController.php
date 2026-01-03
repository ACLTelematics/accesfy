<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } elseif ($user instanceof \App\Models\SuperUser) {
            // SuperUser puede ver cualquier gym, por defecto el primero o especificado
            $gymOwnerId = $request->input('gym_owner_id', 1);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Obtener todas las configuraciones como objeto plano
        $settings = Setting::where('gym_owner_id', $gymOwnerId)
            ->get()
            ->pluck('value', 'key')
            ->toArray();

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } elseif ($user instanceof \App\Models\SuperUser) {
            $gymOwnerId = $request->input('gym_owner_id', 1);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validar campos básicos
        $validated = $request->validate([
            'gym_name' => 'sometimes|string|max:255',
            'gym_slogan' => 'sometimes|nullable|string|max:255',
            'gym_description' => 'sometimes|nullable|string',
            'gym_address' => 'sometimes|nullable|string',
            'gym_phone' => 'sometimes|nullable|string|max:20',
            'gym_email' => 'sometimes|nullable|email',
            'gym_website' => 'sometimes|nullable|url',
            'opening_hours' => 'sometimes|nullable|json',
            'social_facebook' => 'sometimes|nullable|url',
            'social_instagram' => 'sometimes|nullable|string',
            'social_twitter' => 'sometimes|nullable|string',
            'social_whatsapp' => 'sometimes|nullable|string',
            'timezone' => 'sometimes|string',
            'currency' => 'sometimes|string|max:3',
            'language' => 'sometimes|string|max:2',
        ]);

        // Guardar cada configuración
        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                [
                    'gym_owner_id' => $gymOwnerId,
                    'key' => $key
                ],
                [
                    'value' => $value
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada correctamente'
        ]);
    }

    public function get(Request $request, $key)
    {
        $user = $request->user();

        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } elseif ($user instanceof \App\Models\SuperUser) {
            $gymOwnerId = $request->input('gym_owner_id', 1);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $setting = Setting::where('gym_owner_id', $gymOwnerId)
            ->where('key', $key)
            ->first();

        return response()->json([
            'key' => $key,
            'value' => $setting?->value
        ]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = $request->user();

        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } elseif ($user instanceof \App\Models\SuperUser) {
            $gymOwnerId = $request->input('gym_owner_id', 1);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Eliminar logo anterior si existe
        $oldLogo = Setting::where('gym_owner_id', $gymOwnerId)
            ->where('key', 'gym_logo')
            ->first();

        if ($oldLogo && Storage::disk('public')->exists($oldLogo->value)) {
            Storage::disk('public')->delete($oldLogo->value);
        }

        // Guardar nuevo logo
        $path = $request->file('logo')->store('logos', 'public');

        Setting::updateOrCreate(
            ['gym_owner_id' => $gymOwnerId, 'key' => 'gym_logo'],
            ['value' => $path]
        );

        return response()->json([
            'success' => true,
            'message' => 'Logo actualizado exitosamente',
            'path' => $path  // Solo el path, el frontend armará la URL completa
        ]);
    }
}
