<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'gym_owner_id',
        'name',
        'email',
        'phone',
        'gender',
        'active',
        'membership_expires',
        'membership_id',
        'is_couple',
        'related_client_id',
        'fingerprint_hash',
        'pin_hash',
        'biometric_enabled',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_couple' => 'boolean',
        'biometric_enabled' => 'boolean',
        'membership_expires' => 'datetime',
    ];

    public function gymOwner()
    {
        return $this->belongsTo(GymOwner::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function relatedClient()
    {
        return $this->belongsTo(Client::class, 'related_client_id');
    }

    public function couplePartner()
    {
        return $this->hasOne(Client::class, 'related_client_id');
    }
}
