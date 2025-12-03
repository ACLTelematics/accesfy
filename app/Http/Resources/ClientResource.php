<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gym_owner_id' => $this->gym_owner_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'active' => $this->active,
            'membership_expires' => $this->membership_expires?->format('Y-m-d H:i:s'),
            'membership' => [
                'id' => $this->membership?->id,
                'type' => $this->membership?->type,
                'price' => $this->membership?->price,
            ],
            'is_couple' => $this->is_couple,
            'related_client' => $this->when($this->relatedClient, [
                'id' => $this->relatedClient?->id,
                'name' => $this->relatedClient?->name,
            ]),
            'biometric_enabled' => $this->biometric_enabled,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}