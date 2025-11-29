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
}