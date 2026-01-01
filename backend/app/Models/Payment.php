<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'gym_owner_id',
        'client_id',
        'staff_id',
        'amount',
        'method',
        'frequency',
        'payment_date',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function gymOwner()
    {
        return $this->belongsTo(GymOwner::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
