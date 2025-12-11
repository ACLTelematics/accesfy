<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'gym_owner_id',
        'key',
        'value',
    ];

    public function gymOwner()
    {
        return $this->belongsTo(GymOwner::class);
    }
}