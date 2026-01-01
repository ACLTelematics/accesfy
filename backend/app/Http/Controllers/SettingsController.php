<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

        $settings = Setting::where('gym_owner_id', $gymOwnerId)->get()->pluck('value', 'key');

        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;
        $data = $request->all();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['gym_owner_id' => $gymOwnerId, 'key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['success' => true, 'message' => 'Configuración actualizada']);
    }

    public function get(Request $request, $key)
    {
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

        $setting = Setting::where('gym_owner_id', $gymOwnerId)
            ->where('key', $key)
            ->first();

        return response()->json(['value' => $setting?->value]);
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

        // Eliminar logo anterior si existe
        $oldLogo = Setting::where('gym_owner_id', $gymOwnerId)
            ->where('key', 'gym_logo')
            ->first();

        if ($oldLogo && \Storage::exists($oldLogo->value)) {
            \Storage::delete($oldLogo->value);
        }

        // Guardar nuevo logo
        $path = $request->file('logo')->store('logos', 'public');

        Setting::updateOrCreate(
            ['gym_owner_id' => $gymOwnerId, 'key' => 'gym_logo'],
            ['value' => $path]
        );

        return response()->json([
            'success' => true,
            'message' => 'Logo actualizado',
            'path' => asset('storage/' . $path)
        ]);
    }
}
