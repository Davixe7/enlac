<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'enlac_responsible' => 'required|boolean',
            'legal_guardian' => 'required|boolean',
            'email' => 'required|email|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'home_phone' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'candidate_id.required' => 'El candidato es requerido.',
            'candidate_id.exists' => 'El candidato seleccionado no existe.',
            'first_name.required' => 'El nombre es requerido.',
            'first_name.string' => 'El nombre debe ser una cadena de texto.',
            'first_name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'last_name.required' => 'El apellido es requerido.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'last_name.max' => 'El apellido no debe exceder los 255 caracteres.',
            'relationship.required' => 'La relación es requerida.',
            'relationship.string' => 'La relación debe ser una cadena de texto.',
            'relationship.max' => 'La relación no debe exceder los 255 caracteres.',
            'enlac_responsible.required' => 'El campo "Enlace Responsable" es requerido.',
            'enlac_responsible.boolean' => 'El campo "Enlace Responsable" debe ser verdadero o falso.',
            'legal_guardian.required' => 'El campo "Representante Legal" es requerido.',
            'legal_guardian.boolean' => 'El campo "Representante Legal" debe ser verdadero o falso.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no debe exceder los 255 caracteres.',
            'whatsapp.string' => 'El número de WhatsApp debe ser una cadena de texto.',
            'whatsapp.max' => 'El número de WhatsApp no debe exceder los 255 caracteres.',
            'home_phone.string' => 'El número de teléfono debe ser una cadena de texto.',
            'home_phone.max' => 'El número de teléfono no debe exceder los 255 caracteres.',
        ];
    }
}
