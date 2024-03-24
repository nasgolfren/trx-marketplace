<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class GeneralService
{

    public function isAddressCorrect(string $address): bool
    {
        $response = Http::withoutVerifying()
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://api.shasta.trongrid.io/wallet/validateaddress', [
                'address' => $address,
                'visible' => true,
            ])
            ->throw()
            ->json();

        return $response['result'];
    }
}
