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
// ============================================================================
// AGREGAR AL FINAL DE ClientController.php (ANTES DE LA LLAVE DE CIERRE)
// ============================================================================

/**
 * Crear membresía pareja
 * POST /api/clients/couple
 */
public function storeCouple(Request $request)
{
    $user = $request->user();

    // Determinar gym_owner_id según el rol
    if ($user instanceof \App\Models\GymOwner) {
        $gymOwnerId = $user->id;
    } elseif ($user instanceof \App\Models\Staff) {
        $gymOwnerId = $user->gym_owner_id;
    } else {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // Validar datos
    $validated = $request->validate([
        // Persona 1
        'person1_name' => 'required|string|max:100',
        'person1_email' => 'nullable|email|unique:clients,email',
        'person1_phone' => 'nullable|string|max:20',
        'person1_gender' => 'nullable|in:male,female,other',
        'person1_fingerprint' => 'required|string', // Hash de huella

        // Persona 2
        'person2_name' => 'required|string|max:100',
        'person2_email' => 'nullable|email|unique:clients,email',
        'person2_phone' => 'nullable|string|max:20',
        'person2_gender' => 'nullable|in:male,female,other',
        'person2_fingerprint' => 'required|string', // Hash de huella (debe ser diferente)

        // Datos compartidos
        'membership_id' => 'required|exists:memberships,id',
    ]);

    // Validar que las huellas sean diferentes
    if ($validated['person1_fingerprint'] === $validated['person2_fingerprint']) {
        return response()->json([
            'error' => 'Las huellas digitales deben ser diferentes para cada persona'
        ], 422);
    }

    // Obtener membresía y calcular fecha de vencimiento
    $membership = \App\Models\Membership::find($validated['membership_id']);
    $expiresDate = now()->addDays($membership->duration_days);

    // Generar PIN compartido de 4 dígitos
    $sharedPin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    $pinHash = \Illuminate\Support\Facades\Hash::make($sharedPin);

    // Crear Cliente 1 (Persona 1)
    $client1 = \App\Models\Client::create([
        'gym_owner_id' => $gymOwnerId,
        'name' => $validated['person1_name'],
        'email' => $validated['person1_email'],
        'phone' => $validated['person1_phone'],
        'gender' => $validated['person1_gender'],
        'membership_id' => $validated['membership_id'],
        'membership_expires' => $expiresDate,
        'pin_hash' => $pinHash,
        'fingerprint_hash' => \Illuminate\Support\Facades\Hash::make($validated['person1_fingerprint']),
        'is_couple' => true,
        'biometric_enabled' => true,
        'active' => true,
    ]);

    // Crear Cliente 2 (Persona 2)
    $client2 = \App\Models\Client::create([
        'gym_owner_id' => $gymOwnerId,
        'name' => $validated['person2_name'],
        'email' => $validated['person2_email'],
        'phone' => $validated['person2_phone'],
        'gender' => $validated['person2_gender'],
        'membership_id' => $validated['membership_id'],
        'membership_expires' => $expiresDate,
        'pin_hash' => $pinHash, // MISMO PIN que persona 1
        'fingerprint_hash' => \Illuminate\Support\Facades\Hash::make($validated['person2_fingerprint']),
        'is_couple' => true,
        'biometric_enabled' => true,
        'active' => true,
    ]);

    // Vincular ambos clientes (bidireccional)
    $client1->update(['related_client_id' => $client2->id]);
    $client2->update(['related_client_id' => $client1->id]);

    return response()->json([
        'success' => true,
        'message' => 'Membresía pareja creada exitosamente',
        'shared_pin' => $sharedPin, // Para mostrar al staff
        'clients' => [
            'person1' => [
                'id' => $client1->id,
                'name' => $client1->name,
                'email' => $client1->email,
            ],
            'person2' => [
                'id' => $client2->id,
                'name' => $client2->name,
                'email' => $client2->email,
            ],
        ],
        'membership_expires' => $expiresDate->format('Y-m-d'),
    ], 201);
}
/**
 * Romper vínculo de membresía pareja
 * POST /api/clients/{client}/break-couple-link
 */
public function breakCoupleLink(Request $request, Client $client)
{
    // Validar que el cliente es parte de una pareja
    if (!$client->is_couple || !$client->related_client_id) {
        return response()->json([
            'error' => 'Este cliente no tiene membresía pareja'
        ], 422);
    }

    // Obtener la pareja
    $partner = \App\Models\Client::find($client->related_client_id);

    if (!$partner) {
        return response()->json([
            'error' => 'No se encontró el cliente relacionado'
        ], 404);
    }

    // Validar opción de disolución
    $validated = $request->validate([
        'option' => 'required|in:both_individual,person1_only,person2_only,deactivate_both'
    ]);

    $gymOwnerId = $client->gym_owner_id;

    // Buscar membresía individual del mismo gym
    $individualMembership = \App\Models\Membership::where('type', 'individual')
        ->where('gym_owner_id', $gymOwnerId)
        ->where('active', true)
        ->first();

    if (!$individualMembership && $validated['option'] !== 'deactivate_both') {
        return response()->json([
            'error' => 'No hay membresía individual disponible en este gimnasio'
        ], 422);
    }

    $result = [];

    switch ($validated['option']) {
        case 'both_individual':
            // Generar nuevos PINs únicos para cada uno
            $newPin1 = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $newPin2 = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            // Actualizar persona 1
            $client->update([
                'is_couple' => false,
                'related_client_id' => null,
                'membership_id' => $individualMembership->id,
                'pin_hash' => \Illuminate\Support\Facades\Hash::make($newPin1),
                // Conserva fingerprint_hash y membership_expires
            ]);

            // Actualizar persona 2
            $partner->update([
                'is_couple' => false,
                'related_client_id' => null,
                'membership_id' => $individualMembership->id,
                'pin_hash' => \Illuminate\Support\Facades\Hash::make($newPin2),
                // Conserva fingerprint_hash y membership_expires
            ]);

            $result = [
                'message' => 'Vínculo disuelto. Ambos convertidos a membresía individual',
                'new_pins' => [
                    $client->name => $newPin1,
                    $partner->name => $newPin2,
                ],
            ];
            break;

        case 'person1_only':
            // Solo persona 1 continúa con individual
            $newPin1 = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $client->update([
                'is_couple' => false,
                'related_client_id' => null,
                'membership_id' => $individualMembership->id,
                'pin_hash' => \Illuminate\Support\Facades\Hash::make($newPin1),
            ]);

            // Desactivar persona 2
            $partner->update([
                'is_couple' => false,
                'related_client_id' => null,
                'active' => false,
            ]);

            $result = [
                'message' => "{$client->name} convertido a individual. {$partner->name} desactivado",
                'new_pins' => [
                    $client->name => $newPin1,
                ],
            ];
            break;

        case 'person2_only':
            // Solo persona 2 continúa con individual
            $newPin2 = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $partner->update([
                'is_couple' => false,
                'related_client_id' => null,
                'membership_id' => $individualMembership->id,
                'pin_hash' => \Illuminate\Support\Facades\Hash::make($newPin2),
            ]);

            // Desactivar persona 1
            $client->update([
                'is_couple' => false,
                'related_client_id' => null,
                'active' => false,
            ]);

            $result = [
                'message' => "{$partner->name} convertido a individual. {$client->name} desactivado",
                'new_pins' => [
                    $partner->name => $newPin2,
                ],
            ];
            break;

        case 'deactivate_both':
            // Desactivar ambos
            $client->update([
                'is_couple' => false,
                'related_client_id' => null,
                'active' => false,
            ]);

            $partner->update([
                'is_couple' => false,
                'related_client_id' => null,
                'active' => false,
            ]);

            $result = [
                'message' => 'Ambos clientes desactivados',
            ];
            break;
    }

    return response()->json([
        'success' => true,
        ...$result,
    ]);
}
}
