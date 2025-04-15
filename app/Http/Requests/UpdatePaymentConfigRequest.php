<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentConfigRequest extends FormRequest
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
            'candidate_id'             => 'required|exists:candidates,id',
            'sponsor_id'               => 'required|exists:sponsors,id',
            'amount'                   => 'required|numeric|min:0',
            'frequency'                => 'required|integer|min:1|max:255',
            'month_payday'             => 'required|integer|min:1|max:31',
            'address_type'             => 'required|in:home,office',
            'wants_pickup'             => 'nullable|boolean',
            'wants_reminder'           => 'nullable|boolean',
            'wants_deductible_receipt' => 'nullable|boolean',
        ];
    }
}
