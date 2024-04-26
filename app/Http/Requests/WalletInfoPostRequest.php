<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletInfoPostRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account' => 'required|array',
            'account_resource' => 'required|array',
            'wallet_address' => 'required|string',
            'wallet_name' => 'required|string',
        ];
    }
}
