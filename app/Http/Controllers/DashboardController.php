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
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

        $stats = [
            'active_members' => Client::where('gym_owner_id', $gymOwnerId)
                ->where('active', true)
                ->where('membership_expires', '>', now())
                ->count(),

            'accesses_today' => Attendance::whereHas('client', function ($q) use ($gymOwnerId) {
                $q->where('gym_owner_id', $gymOwnerId);
            })
                ->whereDate('check_in', today())
                ->count(),

            'current_occupancy' => Attendance::whereHas('client', function ($q) use ($gymOwnerId) {
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
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

        $activities = Attendance::with('client')
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
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

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
                    'days_left' => max(0, $daysLeft),
                ];
            });

        return response()->json($members);
    }

    public function membershipDistribution(Request $request)
    {
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

        $distribution = Client::where('gym_owner_id', $gymOwnerId)
            ->where('active', true)
            ->join('memberships', 'clients.membership_id', '=', 'memberships.id')
            ->select('memberships.type', DB::raw('count(*) as count'))
            ->groupBy('memberships.type')
            ->get();

        return response()->json($distribution);
    }

    public function peakHours(Request $request)
    {
        $gymOwnerId = $request->user()->gym_owner_id ?? $request->user()->id;

        $peakHours = Attendance::whereHas('client', function ($q) use ($gymOwnerId) {
            $q->where('gym_owner_id', $gymOwnerId);
        })
            ->whereDate('check_in', '>=', now()->subDays(7))
            ->select(DB::raw('HOUR(check_in) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                $hour = $item->hour;
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
}