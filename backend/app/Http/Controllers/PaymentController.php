<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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
        ]);

        $payment = Payment::create($validated);

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
            ->get();

        return response()->json($payments);
    }

    public function getByGymOwner($gymOwnerId)
    {
        $payments = Payment::where('gym_owner_id', $gymOwnerId)
            ->with('client', 'staff')
            ->get();

        return response()->json($payments);
    }
}