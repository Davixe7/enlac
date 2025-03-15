<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterviewRequest extends FormRequest
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
            'candidate_id' => 'nullable|exists:candidates,id',
            'content' => 'required|string',
            'apgar_rank' => 'nullable|integer|min:1|max:10',
            'sphincters_control' => 'nullable|boolean',
            'observation' => 'nullable|string',
            'signed_at' => 'nullable|date',
        ];
    }
}
