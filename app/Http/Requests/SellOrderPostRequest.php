<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
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
        return [
            function (Validator $validator) {

                if (empty($this->request->get('resource'))) {
                    $validator->errors()->add(
                        'resource',
                        'The resource field is required.'
                    );

                    return;
                }

                $formConfig = config('app.formConfig')[$this->request->get('resource')];
                $minAmountValue = Arr::get($formConfig, 'minSellAmountValue');

                if ($this->request->get('amount') < $minAmountValue) {
                    $validator->errors()->add(
                        'amount',
                        'The minimum amount is ' . $minAmountValue . '.',
                    );

                    return;
                }
            }
        ];
    }
}
