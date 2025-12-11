<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'staff_id' => 'required|exists:staff,id',
            'check_in' => 'required|date',
            'check_out' => 'nullable|date|after:check_in',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'El cliente es obligatorio',
            'client_id.exists' => 'El cliente no existe',
            'staff_id.required' => 'El staff es obligatorio',
            'staff_id.exists' => 'El staff no existe',
            'check_in.required' => 'La hora de entrada es obligatoria',
            'check_in.date' => 'La hora de entrada debe ser una fecha válida',
            'check_out.date' => 'La hora de salida debe ser una fecha válida',
            'check_out.after' => 'La hora de salida debe ser después de la entrada',
        ];
    }
}