<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_id'   => ['required','exists:candidates,id'],
            'sponsor_id'     => [
                Rule::requiredIf(fn () => $this->input('payment_type') === 'sponsor'),
                'nullable',
                'exists:sponsors,id',
            ],
            'payment_type'   => ['required', Rule::in(['parent','sponsor'])],
            'is_partial'     => ['nullable', Rule::in([0,1])],
            'date'           => ['required','date'],
            'payment_method' => ['required','string','max:255'],
            'ref'            => ['nullable','string','max:255'],
            'comments'       => ['nullable','string'],
            'amount'         => ['required','numeric','min:0'],
        ];
}

}
