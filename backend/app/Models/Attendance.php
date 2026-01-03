<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'staff_id',
        'check_in',
        'check_out',
        'pin_used',
        'biometric_used',
        'status',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'pin_used' => 'boolean',
        'biometric_used' => 'boolean',
    ];

    /**
     * Relación con Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con Staff
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
