<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GymOwnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'super_user_id' => $this->super_user_id,
            'name' => $this->name,
            'email' => $this->email,
            'active' => $this->active,
            'activated_until' => $this->activated_until?->format('Y-m-d'),
            'gym_name' => $this->gym_name,
            'is_expired' => $this->activated_until && $this->activated_until < now(),
            'staff_count' => $this->whenCounted('staff'),
            'clients_count' => $this->whenCounted('clients'),
            'memberships_count' => $this->whenCounted('memberships'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}