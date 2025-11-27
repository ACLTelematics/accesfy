<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'gym_owner_id',
        'user_type',
        'user_id',
        'action',
        'description',
    ];

    public function gymOwner()
    {
        return $this->belongsTo(GymOwner::class);
    }

    public function user()
    {
        return $this->morphTo();
    }
}