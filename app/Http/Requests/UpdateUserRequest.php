<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'name'             => 'sometimes|required|string|max:255',
            'last_name'        => 'sometimes|required|string|max:255',
            'second_last_name' => 'sometimes|required|string|max:255',
            'work_area_id'     => 'nullable|exists:work_areas,id',
            'leader_id'        => 'nullable|exists:users,id',
            'phone'            => 'nullable|string|max:20',
            'entry_date'       => 'nullable|date',
            'status'           => 'nullable|boolean',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')), // Ignora el usuario actual en la validación unique
            ],
            'password' => ['nullable', 'confirmed', Password::min(6)->max(12)],
        ];
    }
}
