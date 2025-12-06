<?php

namespace App\Providers;

use App\Models\SuperUser;
use App\Models\GymOwner;
use App\Models\Client;
use App\Models\Backup;
use App\Policies\SuperUserPolicy;
use App\Policies\GymOwnerPolicy;
use App\Policies\ClientPolicy;
use App\Policies\BackupPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        SuperUser::class => SuperUserPolicy::class,
        GymOwner::class => GymOwnerPolicy::class,
        Client::class => ClientPolicy::class,
        Backup::class => BackupPolicy::class,
        Membership::class => MembershipPolicy::class, 
    ];

    public function boot(): void
    {
        //
    }
}
