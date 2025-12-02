<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Client::class);
        
        $clients = Client::with('gymOwner', 'membership', 'attendances', 'payments')->get();
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Client::class);
        $validated = $request->validate([
            'gym_owner_id' => 'required|exists:gym_owners,id',
            'name' => 'required|string|max:150',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'membership_id' => 'nullable|exists:memberships,id',
            'membership_expires' => 'nullable|datetime',
            'is_couple' => 'sometimes|boolean',
            'related_client_id' => 'nullable|exists:clients,id',
            'fingerprint_hash' => 'nullable|string',
            'pin_hash' => 'nullable|string',
            'biometric_enabled' => 'sometimes|boolean',
        ]);

        $validated['active'] = true;

        $client = Client::create($validated);

        return response()->json($client, 201);
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);
        
        $client->load('gymOwner', 'membership', 'attendances', 'payments', 'relatedClient');
        return response()->json($client);
    }

    public function update(Request $request, Client $client)
    {
        $this->authorize('update', $client);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'email' => 'sometimes|nullable|email',
            'phone' => 'sometimes|nullable|string|max:50',
            'membership_id' => 'sometimes|nullable|exists:memberships,id',
            'membership_expires' => 'sometimes|nullable|datetime',
            'is_couple' => 'sometimes|boolean',
            'related_client_id' => 'sometimes|nullable|exists:clients,id',
            'fingerprint_hash' => 'sometimes|nullable|string',
            'pin_hash' => 'sometimes|nullable|string',
            'biometric_enabled' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
        ]);

        $client->update($validated);

        return response()->json($client);
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        
        $client->delete();

        return response()->json(null, 204);
    }
}