<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'candidate_id'   => 'sometimes|required|exists:candidates,id',
            'sponsor_id'     => 'sometimes|required|exists:sponsors,id',
            'payment_type'   => 'sometimes|required',
            'is_partial'     => 'nullable|boolean',
            'date'           => 'sometimes|required|date',
            'payment_method' => 'sometimes|required|string|max:255',
            'ref'            => 'nullable|string|max:255',
            'comments'       => 'nullable|string',
            'amount'         => 'sometimes|required|numeric|min:0',
        ];
    }
}
