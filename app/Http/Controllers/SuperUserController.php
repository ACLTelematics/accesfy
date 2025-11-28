<?php

namespace App\Http\Controllers;

use App\Models\SuperUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperUserController extends Controller
{
    public function index()
    {
        $superUsers = SuperUser::all();
        return response()->json($superUsers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:super_users',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $superUser = SuperUser::create($validated);

        return response()->json($superUser, 201);
    }

    public function show(SuperUser $superUser)
    {
        return response()->json($superUser);
    }

    public function update(Request $request, SuperUser $superUser)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:super_users,email,' . $superUser->id,
            'password' => 'sometimes|string|min:6',
            'active' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $superUser->update($validated);

        return response()->json($superUser);
    }

    public function destroy(SuperUser $superUser)
    {
        $superUser->delete();

        return response()->json(null, 204);
    }
}