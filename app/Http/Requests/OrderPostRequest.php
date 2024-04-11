<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderPostRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxValue = PHP_INT_MAX;
        $maxBigIntValue = '18446744073709551615';

        return [
            'amount' => 'required|integer|min:1000|max:' . $maxBigIntValue,
            'resource' => 'required|string|max:20',
            'hours' => 'required|integer|min:1|max: ' . $maxValue,
            'price' => 'required|integer|min:10|max: ' . $maxBigIntValue,
            'source_address' => 'required|string|min:34|max:100',
            'target_address' => 'required|string|min:34|max:100',
            'partial_fill' => 'required|boolean',
            'multisignature' => 'required|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'target_address.required' => 'A title is required',
        ];
    }
}
