<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SellOrderPostRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $this->merge(['unique_id' => $this->route('unique_id')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxBigIntValue = '18446744073709551615';

        return [
            'unique_id' => 'required|uuid|exists:orders,unique_id',
            'resource' => 'required|string|max:20',

            'payout_target_address' => 'required|string|min:34|max:100',

            'amount' => 'required|integer|max:' . $maxBigIntValue,
            'reward' => 'required|numeric',
            'total_reward' => 'required|numeric',

            //'txid' => 'required|string|max:190',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     */
    public function after(): array
    {
        $formConfig = config('app.formConfig')[$this->request->get('resource')];

        return [
            function (Validator $validator) {

                // TODO - min amount validation

                // if ($this->somethingElseIsInvalid()) {
                //     $validator->errors()->add(
                //         'amount',
                //         'Minimum amount is !'
                //     );
                // }
            }
        ];
    }
}
