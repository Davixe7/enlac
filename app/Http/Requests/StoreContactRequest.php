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
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'relationship' => 'required|string|max:255',
            'enlac_responsible' => 'nullable|boolean',
            'legal_guardian' => 'nullable|boolean',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'home_phone' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array {
        return [
            'first_name'   => 'primer nombre',
            'middle_name'  => 'apellido materno',
            'last_name'    => 'apellido paterno',
            'relationship' => 'parentesco',
        ];
    }
}
