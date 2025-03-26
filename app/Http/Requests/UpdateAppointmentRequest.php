<?php

namespace App\Http\Requests;

use App\Models\Appointment;
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

    public function attributes()
    {
        return [
            'appointment_type' => 'tipo de cita',
            'date' => 'fecha',
            'observation' => 'observación',
        ];
    }
}
