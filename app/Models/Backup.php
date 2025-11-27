<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'gym_owner_id',
        'created_by',
        'file_path',
        'restorable',
        'is_applied',
        'applied_by',
        'applied_at',
    ];

    protected $casts = [
        'restorable' => 'boolean',
        'is_applied' => 'boolean',
        'applied_at' => 'datetime',
    ];

    public function gymOwner()
    {
        return $this->belongsTo(GymOwner::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(SuperUser::class, 'created_by');
    }

    public function appliedBy()
    {
        return $this->belongsTo(SuperUser::class, 'applied_by');
    }
}