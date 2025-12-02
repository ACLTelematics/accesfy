<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\GymOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Backup::class);
        
        $backups = Backup::with('gymOwner', 'createdBy', 'appliedBy')->get();
        return response()->json($backups);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Backup::class);
        $validated = $request->validate([
            'gym_owner_id' => 'required|exists:gym_owners,id',
            'file_path' => 'required|string',
        ]);

        $backup = Backup::create([
            'gym_owner_id' => $validated['gym_owner_id'],
            'created_by' => auth()->user()->id, // SuperUser o GymOwner
            'file_path' => $validated['file_path'],
            'restorable' => true,
            'is_applied' => false,
            'applied_by' => null,
            'applied_at' => null,
        ]);

        return response()->json($backup, 201);
    }

    public function show(Backup $backup)
    {
        $this->authorize('view', $backup);
        
        $backup->load('gymOwner', 'createdBy', 'appliedBy');
        return response()->json($backup);
    }

    public function update(Request $request, Backup $backup)
    {
        $this->authorize('update', $backup);
        $validated = $request->validate([
            'restorable' => 'sometimes|boolean',
        ]);

        $backup->update($validated);

        return response()->json($backup);
    }

    public function destroy(Backup $backup)
    {
        $this->authorize('delete', $backup);
        
        $backup->delete();

        return response()->json(null, 204);
    }

    public function apply(Backup $backup)
    {
        $this->authorize('apply', $backup);

        $backup->update([
            'is_applied' => true,
            'applied_by' => auth()->user()->id,
            'applied_at' => now(),
        ]);

        return response()->json($backup);
    }

    public function getByGymOwner($gymOwnerId)
    {
        $backups = Backup::where('gym_owner_id', $gymOwnerId)
            ->with('createdBy', 'appliedBy')
            ->get();

        return response()->json($backups);
    }
}