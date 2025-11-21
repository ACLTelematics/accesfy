<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'gym_owner_id',
        'name',
        'email',
        'phone',
        'role',
        'username',
        'password',
    ];

    public function owner()
    {
        return $this->belongsTo(GymOwner::class, 'gym_owner_id');
    }
}
