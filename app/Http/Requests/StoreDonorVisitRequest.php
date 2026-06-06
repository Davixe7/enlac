<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonorVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donor_id'               => 'required|exists:donors,id',
            'visit_date'             => 'required|date_format:Y-m-d',
            'enlac_user_id'          => 'required|exists:users,id',
            'reason'                 => 'required|string|max:500', // * Motivo
            'recommended_by'         => 'nullable|string|max:255',
            'schedule_contact_name'  => 'nullable|string|max:255',
            'schedule_contact_phone' => 'nullable|string|max:20',
            'received_by'            => 'nullable|string|max:255',
            'visitors_names'         => 'nullable|string',
            'material_presented'     => 'nullable|string',
            'result'                 => 'nullable|string',
            'comments'               => 'nullable|string',
            'interests_hobbies'      => 'nullable|string',
        ];
    }

    /**
     * Define nombres amigables para los campos.
     */
    public function attributes(): array
    {
        return [
            'donor_id'               => 'donante',
            'visit_date'             => 'fecha de la visita',
            'enlac_user_id'          => 'responsable de la visita',
            'reason'                 => 'motivo de la visita',
            'recommended_by'         => 'quien recomienda',
            'schedule_contact_name'  => 'nombre del contacto',
            'schedule_contact_phone' => 'celular del contacto',
            'received_by'            => 'persona que recibe',
            'visitors_names'         => 'nombres de los visitantes',
            'material_presented'     => 'material presentado',
            'result'                 => 'resultado',
            'comments'               => 'comentarios',
            'interests_hobbies'      => 'intereses y hobbies',
        ];
    }
}
