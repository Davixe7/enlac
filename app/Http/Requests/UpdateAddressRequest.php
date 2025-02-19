<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'contact_id' => 'exists:contacts,id',
            'street' => 'string|max:255',
            'neighborhood' => 'string|max:255',
            'state' => 'string|max:255',
            'postal_code' => 'string|max:255',
            'exterior_number' => 'nullable|string|max:255',
            'city' => 'string|max:255',
            'country' => 'string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_id.exists' => 'El contacto seleccionado no existe.',
            'street.string' => 'La calle debe ser una cadena de texto.',
            'street.max' => 'La calle no debe exceder los 255 caracteres.',
            'neighborhood.string' => 'La colonia/barrio debe ser una cadena de texto.',
            'neighborhood.max' => 'La colonia/barrio no debe exceder los 255 caracteres.',
            'state.string' => 'El estado/provincia debe ser una cadena de texto.',
            'state.max' => 'El estado/provincia no debe exceder los 255 caracteres.',
            'postal_code.string' => 'El código postal debe ser una cadena de texto.',
            'postal_code.max' => 'El código postal no debe exceder los 255 caracteres.',
            'exterior_number.string' => 'El número exterior debe ser una cadena de texto.',
            'exterior_number.max' => 'El número exterior no debe exceder los 255 caracteres.',
            'city.string' => 'La ciudad debe ser una cadena de texto.',
            'city.max' => 'La ciudad no debe exceder los 255 caracteres.',
            'country.string' => 'El país debe ser una cadena de texto.',
            'country.max' => 'El país no debe exceder los 255 caracteres.',
        ];
    }
}
