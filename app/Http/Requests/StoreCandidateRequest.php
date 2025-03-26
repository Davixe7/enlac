<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
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
            'candidate.first_name'   => 'required|string|max:255',
            'candidate.middle_name'  => 'required|string|max:255',
            'candidate.last_name'    => 'required|string|max:255',
            'candidate.birth_date'   => 'required|date',
            'candidate.diagnosis'    => 'required|string',
            'candidate.photo'        => 'nullable|string',
            'candidate.info_channel' => 'required',
            'candidate.sheet'        => 'nullable',

            'contacts'                => 'required|array',
            'contacts.*.first_name'   => 'required|string|max:255',
            'contacts.*.middle_name'  => 'nullable|string|max:255',
            'contacts.*.last_name'    => 'required|string|max:255',
            'contacts.*.relationship' => 'required|string|max:255',

            'medications.*.name'         => 'required',
            'medications.*.dose'         => 'required',
            'medications.*.frequency'    => 'required',
            'medications.*.duration'     => 'required',
            'medications.*.observations' => 'nullable|string|max:255',

            'evaluation_schedule.evaluator_id' => 'required',
            'evaluation_schedule.date'         => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'candidate.first_name'   => 'nombre',
            'candidate.middle_name'  => 'apellido materno',
            'candidate.last_name'    => 'apellido paterno',
            'candidate.birth_date'   => 'fecha de nacimiento',
            'candidate.diagnosis'    => 'diagnostico',
            'candidate.photo'        => 'foto',
            'candidate.sheet'        => 'folio',

            'contacts.*.first_name'   => 'primer nombre',
            'contacts.*.middle_name'  => 'apellido materno',
            'contacts.*.last_name'    => 'apellido paterno',
            'contacts.*.relationship' => 'parentesco',

            'medications.*.name'         => 'nombre',
            'medications.*.dose'         => 'dosis',
            'medications.*.frequency'    => 'frecuencia',
            'medications.*.duration'     => 'duracion',
            'medications.*.observations' => 'observaciones',
        ];
    }
}
