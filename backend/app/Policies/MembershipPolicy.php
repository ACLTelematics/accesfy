<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\GymOwner;
use App\Models\Staff;
use App\Models\SuperUser;

class MembershipPolicy
{
    public function viewAny($user): bool
    {
        return true; // Todos pueden ver
    }

    public function view($user, Membership $membership): bool
    {
        return true; // Todos pueden ver una membresía
    }

    public function create($user): bool
    {
        return $user instanceof SuperUser || $user instanceof GymOwner;
    }

    public function update($user, Membership $membership): bool
    {
        if ($user instanceof SuperUser) {
            return true;
        }

        if ($user instanceof GymOwner) {
            return $membership->gym_owner_id === $user->id;
        }

        return false;
    }

    public function delete($user, Membership $membership): bool
    {
        if ($user instanceof SuperUser) {
            return true;
        }

        if ($user instanceof GymOwner) {
            return $membership->gym_owner_id === $user->id;
        }

        return false;
    }
}
