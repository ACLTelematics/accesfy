<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Client;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('client', 'staff')->get();
        return response()->json($attendances);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'required|exists:staff,id',
            'check_in' => 'required|datetime',
            'check_out' => 'nullable|datetime',
        ]);

        $validated['status'] = 'active';

        $attendance = Attendance::create($validated);

        return response()->json($attendance, 201);
    }

    public function show(Attendance $attendance)
    {
        $attendance->load('client', 'staff');
        return response()->json($attendance);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'check_out' => 'sometimes|nullable|datetime',
            'status' => 'sometimes|string',
        ]);

        $attendance->update($validated);

        return response()->json($attendance);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return response()->json(null, 204);
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'required|exists:staff,id',
        ]);

        $attendance = Attendance::create([
            'client_id' => $validated['client_id'],
            'staff_id' => $validated['staff_id'],
            'check_in' => now(),
            'status' => 'active',
        ]);

        return response()->json($attendance, 201);
    }

    public function checkOut(Attendance $attendance)
    {
        $attendance->update([
            'check_out' => now(),
            'status' => 'completed',
        ]);

        return response()->json($attendance);
    }

    /**
     * Check-in con PIN o huella digital
     */
    public function checkInBiometric(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'nullable|string',
            'fingerprint' => 'nullable|string',
        ]);

        // Buscar TODOS los clientes activos
        $clients = Client::where('active', true)->get();

        $client = null;

        // Buscar por PIN (validando con Hash::check)
        if (isset($validated['pin'])) {
            foreach ($clients as $c) {
                if ($c->pin_hash && \Hash::check($validated['pin'], $c->pin_hash)) {
                    $client = $c;
                    break;
                }
            }
        }

        // Buscar por huella digital (validando con Hash::check)
        if (!$client && isset($validated['fingerprint'])) {
            foreach ($clients as $c) {
                if ($c->fingerprint_hash && \Hash::check($validated['fingerprint'], $c->fingerprint_hash)) {
                    $client = $c;
                    break;
                }
            }
        }

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'PIN o huella incorrectos'
            ], 404);
        }

        // Verificar membresía vencida
        if ($client->membership_expires && $client->membership_expires < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Membresía vencida',
                'client' => [
                    'name' => $client->name,
                    'membership_expires' => $client->membership_expires->format('Y-m-d'),
                ]
            ], 403);
        }

        // Verificar si ya tiene check-in hoy
        $activeAttendance = Attendance::where('client_id', $client->id)
            ->whereDate('check_in', today())
            ->first();

        if ($activeAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tiene un acceso registrado hoy',
                'client' => [
                    'name' => $client->name,
                    'check_in' => $activeAttendance->check_in->format('H:i'),
                ]
            ], 400);
        }

        // Registrar check-in exitoso
        $attendance = Attendance::create([
            'client_id' => $client->id,
            'staff_id' => null,
            'check_in' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Acceso concedido',
            'client' => [
                'name' => $client->name,
                'membership_type' => $client->membership?->type,
                'membership_status' => $client->membership_expires > now() ? 'Activa' : 'Vencida',
            ],
            'attendance' => [
                'check_in' => $attendance->check_in->format('H:i'),
            ]
        ]);
    }
}
