<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'name'             => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'second_last_name' => 'required|string|max:255',
            'work_area_id'     => 'nullable|exists:work_areas,id',
            'leader_id'        => 'nullable|exists:users,id',
            'phone'            => 'nullable|string|max:20',
            'entry_date'       => 'nullable|date',
            'status'           => 'nullable|boolean',
            'email'            => 'required|string|email|max:255|unique:users,email',
            'password'         => ['required', 'confirmed', Password::min(6)->max(12)],
        ];
    }
}
