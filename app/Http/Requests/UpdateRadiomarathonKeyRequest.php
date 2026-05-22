<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRadiomarathonKeyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // Ignoramos el ID actual para que permita guardar el mismo código
        $id = $this->route('radiomarathon_key') ?? $this->route('id');

        return [
            'code' => "sometimes|required|string|unique:radiomarathon_keys,code,{$id}",
            'classification' => 'sometimes|required|string',
            'concept' => 'sometimes|required|string',
            'is_active' => 'sometimes|required|boolean'
        ];
    }
}
