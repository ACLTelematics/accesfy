<?php

namespace App\Policies;

use App\Models\SuperUser;
use App\Models\GymOwner;
use App\Models\Membership;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy
{
    use HandlesAuthorization;

    public function viewAny($user)
    {
        return $user instanceof SuperUser 
            || $user instanceof GymOwner;
    }

    public function view($user, Membership $membership)
    {
        if ($user instanceof SuperUser) {
            return true;
        }

        return $user instanceof GymOwner && $user->id === $membership->gym_owner_id;
    }

    public function create($user)
    {
        return $user instanceof SuperUser 
            || $user instanceof GymOwner;
    }

    public function update($user, Membership $membership)
    {
        if ($user instanceof SuperUser) {
            return true;
        }

        return $user instanceof GymOwner && $user->id === $membership->gym_owner_id;
    }

    public function delete($user, Membership $membership)
    {
        if ($user instanceof SuperUser) {
            return true;
        }

        return $user instanceof GymOwner && $user->id === $membership->gym_owner_id;
    }
}