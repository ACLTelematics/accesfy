<?php

namespace App\Policies;

use App\Models\SuperUser;
use App\Models\GymOwner;
use App\Models\Backup;
use Illuminate\Auth\Access\HandlesAuthorization;

class BackupPolicy
{
    use HandlesAuthorization;

    public function viewAny($user)
    {
        // SuperUser y GymOwner pueden ver backups
        return $user instanceof SuperUser || $user instanceof GymOwner;
    }

    public function view($user, Backup $backup)
    {
        // SuperUser puede ver todos
        if ($user instanceof SuperUser) {
            return true;
        }

        // GymOwner puede ver solo sus backups
        if ($user instanceof GymOwner) {
            return $user->id === $backup->gym_owner_id;
        }

        return false;
    }

    public function create($user)
    {
        // Solo SuperUser y GymOwner pueden crear backups
        return $user instanceof SuperUser || $user instanceof GymOwner;
    }

    public function update($user, Backup $backup)
    {
        // Solo SuperUser puede actualizar backups
        return $user instanceof SuperUser;
    }

    public function delete($user, Backup $backup)
    {
        // Solo SuperUser puede eliminar backups
        return $user instanceof SuperUser;
    }

    public function apply($user, Backup $backup)
    {
        // Solo SuperUser puede aplicar backups
        return $user instanceof SuperUser;
    }
}