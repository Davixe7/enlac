<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'contact_id' => 'required|exists:contacts,id',
            'street' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'exterior_number' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_id.required' => 'El contacto es requerido.',
            'contact_id.exists' => 'El contacto seleccionado no existe.',
            'street.required' => 'La calle es requerida.',
            'street.string' => 'La calle debe ser una cadena de texto.',
            'street.max' => 'La calle no debe exceder los 255 caracteres.',
            'neighborhood.required' => 'La colonia/barrio es requerida.',
            'neighborhood.string' => 'La colonia/barrio debe ser una cadena de texto.',
            'neighborhood.max' => 'La colonia/barrio no debe exceder los 255 caracteres.',
            'state.required' => 'El estado/provincia es requerido.',
            'state.string' => 'El estado/provincia debe ser una cadena de texto.',
            'state.max' => 'El estado/provincia no debe exceder los 255 caracteres.',
            'postal_code.required' => 'El código postal es requerido.',
            'postal_code.string' => 'El código postal debe ser una cadena de texto.',
            'postal_code.max' => 'El código postal no debe exceder los 255 caracteres.',
            'exterior_number.string' => 'El número exterior debe ser una cadena de texto.',
            'exterior_number.max' => 'El número exterior no debe exceder los 255 caracteres.',
            'city.required' => 'La ciudad es requerida.',
            'city.string' => 'La ciudad debe ser una cadena de texto.',
            'city.max' => 'La ciudad no debe exceder los 255 caracteres.',
            'country.required' => 'El país es requerido.',
            'country.string' => 'El país debe ser una cadena de texto.',
            'country.max' => 'El país no debe exceder los 255 caracteres.',
        ];
    }
}
