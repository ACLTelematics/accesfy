<?php

namespace App\Policies;

use App\Models\SuperUser;
use App\Models\GymOwner;
use App\Models\Staff;
use App\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function viewAny($user)
    {
        // SuperUser, GymOwner y Staff pueden ver clientes
        return $user instanceof SuperUser 
            || $user instanceof GymOwner 
            || $user instanceof Staff;
    }

    public function view($user, Client $client)
    {
        // SuperUser puede ver todos
        if ($user instanceof SuperUser) {
            return true;
        }

        // GymOwner puede ver solo sus clientes
        if ($user instanceof GymOwner) {
            return $user->id === $client->gym_owner_id;
        }

        // Staff puede ver solo clientes de su gimnasio
        if ($user instanceof Staff) {
            return $user->gym_owner_id === $client->gym_owner_id;
        }

        return false;
    }

    public function create($user)
    {
        // SuperUser, GymOwner y Staff pueden crear clientes
        return $user instanceof SuperUser 
            || $user instanceof GymOwner 
            || $user instanceof Staff;
    }

    public function update($user, Client $client)
    {
        // SuperUser puede actualizar todos
        if ($user instanceof SuperUser) {
            return true;
        }

        // GymOwner puede actualizar solo sus clientes
        if ($user instanceof GymOwner) {
            return $user->id === $client->gym_owner_id;
        }

        // Staff puede actualizar solo clientes de su gimnasio
        if ($user instanceof Staff) {
            return $user->gym_owner_id === $client->gym_owner_id;
        }

        return false;
    }

    public function delete($user, Client $client)
    {
        // Solo SuperUser y GymOwner pueden eliminar clientes
        if ($user instanceof SuperUser) {
            return true;
        }

        if ($user instanceof GymOwner) {
            return $user->id === $client->gym_owner_id;
        }

        return false;
    }
}