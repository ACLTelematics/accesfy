<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_owner_id' => 'required|exists:gym_owners,id',
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'membership_id' => 'nullable|exists:memberships,id',
            'membership_expires' => 'nullable|date',
            'is_couple' => 'boolean',
            'related_client_id' => 'nullable|exists:clients,id',
            'fingerprint_hash' => 'nullable|string',
            'pin_hash' => 'nullable|string|min:4',
            'biometric_enabled' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'gym_owner_id.required' => 'El gimnasio es obligatorio',
            'gym_owner_id.exists' => 'El gimnasio no existe',
            'name.required' => 'El nombre es obligatorio',
            'name.max' => 'El nombre no puede tener más de 150 caracteres',
            'email.email' => 'El email debe ser válido',
            'membership_id.exists' => 'La membresía no existe',
            'related_client_id.exists' => 'El cliente relacionado no existe',
            'pin_hash.min' => 'El PIN debe tener al menos 4 caracteres',
        ];
    }
}
