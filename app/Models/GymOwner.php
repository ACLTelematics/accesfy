<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymOwner extends Model
{
    protected $fillable = [
        'super_user_id',
        'name',
        'email',
        'password',
        'active',
        'activated_until',
        'gym_name',
    ];

    public function superUser()
    {
        return $this->belongsTo(SuperUser::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, Client::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function backups()
    {
        return $this->hasMany(Backup::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
}