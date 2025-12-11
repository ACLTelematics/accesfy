<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:150',
            'email' => 'sometimes|nullable|email|max:150',
            'phone' => 'sometimes|nullable|string|max:50',
            'membership_id' => 'sometimes|nullable|exists:memberships,id',
            'membership_expires' => 'sometimes|nullable|date',
            'is_couple' => 'sometimes|boolean',
            'related_client_id' => 'sometimes|nullable|exists:clients,id',
            'fingerprint_hash' => 'sometimes|nullable|string',
            'pin_hash' => 'sometimes|nullable|string|min:4',
            'biometric_enabled' => 'sometimes|boolean',
            'active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'El nombre no puede tener más de 150 caracteres',
            'email.email' => 'El email debe ser válido',
            'membership_id.exists' => 'La membresía no existe',
            'related_client_id.exists' => 'El cliente relacionado no existe',
            'pin_hash.min' => 'El PIN debe tener al menos 4 caracteres',
        ];
    }
}