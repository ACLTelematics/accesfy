<?php

namespace App\Policies;

use App\Models\SuperUser;
use App\Models\GymOwner;
use Illuminate\Auth\Access\HandlesAuthorization;

class GymOwnerPolicy
{
    use HandlesAuthorization;

    public function viewAny($user)
    {
        // SuperUser puede ver todos, GymOwner solo el suyo
        return $user instanceof SuperUser || $user instanceof GymOwner;
    }

    public function view($user, GymOwner $gymOwner)
    {
        // SuperUser puede ver todos, GymOwner solo el suyo
        if ($user instanceof SuperUser) {
            return true;
        }

        return $user instanceof GymOwner && $user->id === $gymOwner->id;
    }

    public function create($user)
    {
        // Solo SuperUser puede crear GymOwners
        return $user instanceof SuperUser;
    }

    public function update($user, GymOwner $gymOwner)
    {
        // SuperUser puede actualizar todos, GymOwner solo el suyo
        if ($user instanceof SuperUser) {
            return true;
        }

        return $user instanceof GymOwner && $user->id === $gymOwner->id;
    }

    public function delete($user, GymOwner $gymOwner)
    {
        // Solo SuperUser puede eliminar GymOwners
        return $user instanceof SuperUser;
    }
}