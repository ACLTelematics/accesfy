<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('gymOwner', 'client', 'staff')->get();
        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'gym_owner_id' => 'required|exists:gym_owners,id',
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'nullable|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,transfer,other',
            'frequency' => 'required|in:daily,monthly,visit',
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
            'extend_membership' => 'sometimes|boolean',
            'deactivate_account' => 'sometimes|boolean',
        ]);

        // Crear el pago
        $payment = Payment::create([
            'gym_owner_id' => $validated['gym_owner_id'],
            'client_id' => $validated['client_id'],
            'staff_id' => $validated['staff_id'] ?? null,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'frequency' => $validated['frequency'],
            'payment_date' => $validated['payment_date'],
            'note' => $validated['note'] ?? null,
        ]);

        // ✨ LÓGICA MEJORADA: Extender según duration_days de la membresía
        if (isset($validated['extend_membership']) && $validated['extend_membership']) {
            $client = Client::with('membership')->find($validated['client_id']);

            if ($client->membership) {
                // Obtener duración desde la membresía
                $durationDays = $client->membership->duration_days;

                // Si ya tiene fecha de vencimiento, sumar desde esa fecha
                // Si no tiene o ya venció, sumar desde hoy
                if ($client->membership_expires && $client->membership_expires > now()) {
                    $client->membership_expires = $client->membership_expires->addDays($durationDays);
                } else {
                    $client->membership_expires = now()->addDays($durationDays);
                }

                $client->save();
            }
        }

        // LÓGICA DE DESACTIVAR CUENTA
        if (isset($validated['deactivate_account']) && $validated['deactivate_account']) {
            $client = Client::find($validated['client_id']);
            $client->active = false;
            $client->save();
        }

        // Recargar relaciones
        $payment->load('gymOwner', 'client', 'staff');

        return response()->json($payment, 201);
    }

    public function show(Payment $payment)
    {
        $payment->load('gymOwner', 'client', 'staff');
        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'method' => 'sometimes|in:cash,card,transfer,other',
            'frequency' => 'sometimes|in:daily,monthly,visit',
            'payment_date' => 'sometimes|date',
            'note' => 'sometimes|nullable|string',
        ]);

        $payment->update($validated);

        return response()->json($payment);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json(null, 204);
    }

    public function getByClient($clientId)
    {
        $payments = Payment::where('client_id', $clientId)
            ->with('gymOwner', 'staff')
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json($payments);
    }

    public function getByGymOwner($gymOwnerId)
    {
        $payments = Payment::where('gym_owner_id', $gymOwnerId)
            ->with('client', 'staff')
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json($payments);
    }
}
