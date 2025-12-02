<?php

namespace App\Policies;

use App\Models\SuperUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuperUserPolicy
{
    use HandlesAuthorization;

    public function viewAny($user)
    {
        // Solo SuperUsers pueden ver la lista
        return $user instanceof SuperUser;
    }

    public function view($user, SuperUser $superUser)
    {
        // Solo SuperUsers pueden ver otros SuperUsers
        return $user instanceof SuperUser;
    }

    public function create($user)
    {
        // Solo SuperUsers pueden crear otros SuperUsers
        return $user instanceof SuperUser;
    }

    public function update($user, SuperUser $superUser)
    {
        // Solo SuperUsers pueden actualizar
        return $user instanceof SuperUser;
    }

    public function delete($user, SuperUser $superUser)
    {
        // Solo SuperUsers pueden eliminar (excepto a sí mismo)
        return $user instanceof SuperUser && $user->id !== $superUser->id;
    }
}