<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::with('gymOwner', 'clients')->get();
        return response()->json($memberships);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gym_owner_id' => 'required|exists:gym_owners,id',
            'type' => 'required|in:individual,couple,student,custom,visit',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'daily_payment' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
        ]);

        $validated['daily_payment'] = $validated['daily_payment'] ?? false;
        $validated['active'] = $validated['active'] ?? true;

        $membership = Membership::create($validated);

        return response()->json($membership, 201);
    }

    public function show(Membership $membership)
    {
        $membership->load('gymOwner', 'clients');
        return response()->json($membership);
    }

    public function update(Request $request, Membership $membership)
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:individual,couple,student,custom,visit',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|nullable|string',
            'daily_payment' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
        ]);

        $membership->update($validated);

        return response()->json($membership);
    }

    public function destroy(Membership $membership)
    {
        $membership->delete();

        return response()->json(null, 204);
    }
}