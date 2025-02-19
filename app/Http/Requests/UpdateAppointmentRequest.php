<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'appointment_type' => [Appointment::APPOINTMENT_TYPES],
            'date' => 'date',
            'time_slot' => 'string',
            'observation' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'appointment_type.in' => 'El tipo de cita seleccionado no es válido.',
            'date.date' => 'La fecha debe ser un formato válido.',
            'observation.string' => 'La observación debe ser una cadena de texto.',
        ];
    }
}
