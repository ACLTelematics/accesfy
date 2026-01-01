<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Membership;
use App\Policies\MembershipPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Membership::class => MembershipPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
