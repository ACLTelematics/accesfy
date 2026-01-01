<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user instanceof \App\Models\SuperUser) {
            $clients = Client::with('membership', 'attendances', 'payments')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user instanceof \App\Models\GymOwner) {
            $clients = Client::where('gym_owner_id', $user->id)
                ->with('membership', 'attendances', 'payments')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user instanceof \App\Models\Staff) {
            $clients = Client::where('gym_owner_id', $user->gym_owner_id)
                ->with('membership', 'attendances', 'payments')
                ->orderBy('created_at', 'desc')
                ->get();

        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female,other',
            'membership_id' => 'nullable|exists:memberships,id',
            'is_couple' => 'boolean',
            'related_client_id' => 'nullable|exists:clients,id',
            'pin' => 'nullable|string|size:4',
            'biometric_enabled' => 'boolean',
        ]);

        $user = $request->user();

        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } elseif ($user instanceof \App\Models\SuperUser) {
            if (!$request->has('gym_owner_id')) {
                return response()->json(['error' => 'SuperUser debe especificar gym_owner_id'], 400);
            }
            $gymOwnerId = $request->gym_owner_id;
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated['gym_owner_id'] = $gymOwnerId;
        $validated['active'] = true;

        // ✨ NUEVA LÓGICA: Calcular fecha de expiración automáticamente
        if (isset($validated['membership_id'])) {
            $membership = Membership::find($validated['membership_id']);
            $validated['membership_expires'] = now()->addDays($membership->duration_days);
        }

        if (isset($validated['pin'])) {
            $validated['pin_hash'] = Hash::make($validated['pin']);
            unset($validated['pin']);
        }

        $client = Client::create($validated);

        return response()->json($client->load('membership'), 201);
    }

    public function show(Client $client)
    {
        return response()->json($client->load(['membership', 'attendances', 'payments', 'relatedClient']));
    }

    public function update(Request $request, Client $client)
    {
        $user = $request->user();

        if ($user instanceof \App\Models\GymOwner) {
            if ($client->gym_owner_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif ($user instanceof \App\Models\Staff) {
            if ($client->gym_owner_id !== $user->gym_owner_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        } elseif (!($user instanceof \App\Models\SuperUser)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'email' => 'sometimes|nullable|email|unique:clients,email,' . $client->id,
            'phone' => 'sometimes|nullable|string|max:50',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'membership_id' => 'sometimes|nullable|exists:memberships,id',
            'active' => 'sometimes|boolean',
            'is_couple' => 'sometimes|boolean',
            'related_client_id' => 'sometimes|nullable|exists:clients,id',
            'pin' => 'sometimes|nullable|string|size:4',
            'biometric_enabled' => 'sometimes|boolean',
        ]);

        // ✨ NUEVA LÓGICA: Si cambió la membresía, recalcular fecha
        if (isset($validated['membership_id']) && $validated['membership_id'] != $client->membership_id) {
            $membership = Membership::find($validated['membership_id']);
            $validated['membership_expires'] = now()->addDays($membership->duration_days);
        }

        if (isset($validated['pin'])) {
            $validated['pin_hash'] = Hash::make($validated['pin']);
            unset($validated['pin']);
        }

        $client->update($validated);

        return response()->json($client->load('membership'));
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $user = $request->user();

        $clientsQuery = Client::query();

        if ($user instanceof \App\Models\GymOwner) {
            $clientsQuery->where('gym_owner_id', $user->id);
        } elseif ($user instanceof \App\Models\Staff) {
            $clientsQuery->where('gym_owner_id', $user->gym_owner_id);
        }

        $clients = $clientsQuery
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->with('membership')
            ->limit(20)
            ->get();

        return response()->json($clients);
    }
}
