<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } else {
            return response()->json([
                'active_members' => 0,
                'accesses_today' => 0,
                'current_occupancy' => 0,
                'expiring_soon' => 0,
            ]);
        }

        $stats = [
            'active_members' => Client::where('gym_owner_id', $gymOwnerId)
                ->where('active', true)
                ->where('membership_expires', '>', now())
                ->count(),

            'accesses_today' => Attendance::query()
                ->whereHas('client', function ($q) use ($gymOwnerId) {
                    $q->where('gym_owner_id', $gymOwnerId);
                })
                ->whereDate('check_in', today())
                ->count(),

            'current_occupancy' => Attendance::query()
                ->whereHas('client', function ($q) use ($gymOwnerId) {
                    $q->where('gym_owner_id', $gymOwnerId);
                })
                ->whereDate('check_in', today())
                ->whereNull('check_out')
                ->count(),

            'expiring_soon' => Client::where('gym_owner_id', $gymOwnerId)
                ->where('active', true)
                ->whereBetween('membership_expires', [now(), now()->addDays(7)])
                ->count(),
        ];

        return response()->json($stats);
    }

    public function recentActivity(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } else {
            return response()->json([]);
        }

        $activities = Attendance::query()
            ->with('client')
            ->whereHas('client', function ($q) use ($gymOwnerId) {
                $q->where('gym_owner_id', $gymOwnerId);
            })
            ->orderBy('check_in', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($attendance) {
                return [
                    'client_name' => $attendance->client->name,
                    'check_in' => $attendance->check_in->diffForHumans(),
                    'status' => $attendance->check_out ? 'completed' : 'active',
                ];
            });

        return response()->json($activities);
    }

    public function expiringMembers(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } else {
            return response()->json([]);
        }

        $members = Client::where('gym_owner_id', $gymOwnerId)
            ->where('active', true)
            ->whereBetween('membership_expires', [now(), now()->addDays(7)])
            ->orderBy('membership_expires', 'asc')
            ->get()
            ->map(function ($client) {
                $daysLeft = now()->diffInDays($client->membership_expires, false);
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'membership_expires' => $client->membership_expires->format('Y-m-d'),
                    'days_left' => max(0, (int)$daysLeft),
                ];
            });

        return response()->json($members);
    }

    public function membershipDistribution(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } else {
            return response()->json([]);
        }

        $distribution = Client::where('clients.gym_owner_id', $gymOwnerId)
            ->where('clients.active', true)
            ->whereNotNull('clients.membership_id')
            ->join('memberships', 'clients.membership_id', '=', 'memberships.id')
            ->select('memberships.type', DB::raw('count(*) as count'))
            ->groupBy('memberships.type')
            ->get();

        return response()->json($distribution);
    }

    public function peakHours(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } else {
            return response()->json([]);
        }

        // ✅ POSTGRESQL: Usar EXTRACT en lugar de HOUR()
        $peakHours = Attendance::query()
            ->whereHas('client', function ($q) use ($gymOwnerId) {
                $q->where('gym_owner_id', $gymOwnerId);
            })
            ->whereDate('check_in', '>=', now()->subDays(7))
            ->select(
                DB::raw('EXTRACT(HOUR FROM check_in) as hour'),  // ← CORREGIDO
                DB::raw('count(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                $hour = (int)$item->hour;
                if ($hour >= 6 && $hour < 10) $range = '6-10am';
                elseif ($hour >= 10 && $hour < 14) $range = '10am-2pm';
                elseif ($hour >= 14 && $hour < 18) $range = '2-6pm';
                else $range = '6-10pm';

                return ['range' => $range, 'count' => $item->count];
            })
            ->groupBy('range')
            ->map(function ($group) {
                return $group->sum('count');
            });

        return response()->json($peakHours);
    }

    public function genderDistribution(Request $request)
    {
        $user = $request->user();

        // Determinar gym_owner_id según el tipo de usuario
        if ($user instanceof \App\Models\GymOwner) {
            $gymOwnerId = $user->id;
        } elseif ($user instanceof \App\Models\Staff) {
            $gymOwnerId = $user->gym_owner_id;
        } else {
            return response()->json([]);
        }

        $distribution = Client::where('gym_owner_id', $gymOwnerId)
            ->where('active', true)
            ->whereNotNull('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = match($item->gender) {
                    'male' => 'Hombres',
                    'female' => 'Mujeres',
                    'other' => 'Otro',
                    default => 'Sin especificar'
                };
                return [$label => $item->count];
            });

        return response()->json($distribution);
    }
/**
 * Obtener asistencias con fechas ISO para reportes
 * (No usa diffForHumans como recentActivity)
 */
public function attendancesForReports(Request $request)
{
    $user = $request->user();

    // Determinar gym_owner_id según el tipo de usuario
    if ($user instanceof \App\Models\GymOwner) {
        $gymOwnerId = $user->id;
    } elseif ($user instanceof \App\Models\Staff) {
        $gymOwnerId = $user->gym_owner_id;
    } else {
        return response()->json([]);
    }

    // ✅ Retornar con fechas ISO reales, NO texto relativo
    $attendances = Attendance::query()
        ->with('client')
        ->whereHas('client', function ($q) use ($gymOwnerId) {
            $q->where('gym_owner_id', $gymOwnerId);
        })
        ->orderBy('check_in', 'desc')
        ->get()
        ->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'client_id' => $attendance->client_id,
                'client_name' => $attendance->client->name ?? 'N/A',
                'check_in' => $attendance->check_in->toISOString(),  // ✅ Fecha ISO
                'check_out' => $attendance->check_out ? $attendance->check_out->toISOString() : null,
                'status' => $attendance->check_out ? 'completed' : 'active',
            ];
        });

    return response()->json($attendances);
}

}
