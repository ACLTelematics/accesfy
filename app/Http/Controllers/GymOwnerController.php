<?php

namespace App\Http\Controllers;

use App\Models\GymOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GymOwnerController extends Controller
{
    public function index()
    {
        $gymOwners = GymOwner::with('superUser')->get();
        return response()->json($gymOwners);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'super_user_id' => 'required|exists:super_users,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:gym_owners',
            'password' => 'required|string|min:6',
            'gym_name' => 'required|string|max:150',
            'activated_until' => 'required|date',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['active'] = true;

        $gymOwner = GymOwner::create($validated);

        return response()->json($gymOwner, 201);
    }

    public function show(GymOwner $gymOwner)
    {
        $gymOwner->load('superUser', 'staff', 'clients', 'memberships');
        return response()->json($gymOwner);
    }

    public function update(Request $request, GymOwner $gymOwner)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:gym_owners,email,' . $gymOwner->id,
            'password' => 'sometimes|string|min:6',
            'gym_name' => 'sometimes|string|max:150',
            'activated_until' => 'sometimes|date',
            'active' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $gymOwner->update($validated);

        return response()->json($gymOwner);
    }

    public function destroy(GymOwner $gymOwner)
    {
        $gymOwner->delete();

        return response()->json(null, 204);
    }
}