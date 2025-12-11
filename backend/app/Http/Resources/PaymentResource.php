<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gym_owner_id' => $this->gym_owner_id,
            'client' => [
                'id' => $this->client?->id,
                'name' => $this->client?->name,
                'email' => $this->client?->email,
            ],
            'staff' => [
                'id' => $this->staff?->id,
                'name' => $this->staff?->name,
            ],
            'amount' => number_format($this->amount, 2),
            'method' => $this->method,
            'frequency' => $this->frequency,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'note' => $this->note,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}