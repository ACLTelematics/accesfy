<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gym_owner_id' => 'required|exists:gym_owners,id',
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'nullable|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,transfer,other',
            'frequency' => 'required|in:daily,monthly,visit',
            'payment_date' => 'required|date',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'gym_owner_id.required' => 'El gimnasio es obligatorio',
            'gym_owner_id.exists' => 'El gimnasio no existe',
            'client_id.required' => 'El cliente es obligatorio',
            'client_id.exists' => 'El cliente no existe',
            'staff_id.exists' => 'El staff no existe',
            'amount.required' => 'El monto es obligatorio',
            'amount.min' => 'El monto debe ser mayor o igual a 0',
            'method.required' => 'El método de pago es obligatorio',
            'method.in' => 'El método debe ser: cash, card, transfer u other',
            'frequency.required' => 'La frecuencia es obligatoria',
            'frequency.in' => 'La frecuencia debe ser: daily, monthly o visit',
            'payment_date.required' => 'La fecha de pago es obligatoria',
            'note.max' => 'La nota no puede tener más de 500 caracteres',
        ];
    }
}