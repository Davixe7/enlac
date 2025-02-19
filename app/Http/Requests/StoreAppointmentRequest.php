<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'candidate_id' => 'required|exists:candidates,id',
            'appointment_type' => ['required', Rule::in(Appointment::APPOINTMENT_TYPES)],
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'observation' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'candidate_id.required' => 'El campo candidato es requerido.',
            'candidate_id.exists' => 'El candidato seleccionado no existe.',
            'appointment_type.required' => 'El tipo de cita es requerido.',
            'appointment_type.in' => 'El tipo de cita seleccionado no es válido.',
            'user_id.required' => 'El campo usuario es requerido.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'date.required' => 'La fecha es requerida.',
            'date.date' => 'La fecha debe ser un formato válido.',
            'time_slot.required' => 'El horario es requerido.',
            'observation.string' => 'La observación debe ser una cadena de texto.',
        ];
    }
}
