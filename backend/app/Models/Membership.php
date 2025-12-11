<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $fillable = [
        'gym_owner_id',
        'type',
        'price',
        'description',
        'daily_payment',
        'active',
    ];

    protected $casts = [
        'daily_payment' => 'boolean',
        'active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function gymOwner()
    {
        return $this->belongsTo(GymOwner::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}