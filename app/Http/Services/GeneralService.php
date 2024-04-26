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

    public function getAccountInfo(string $address)
    {
        $response = Http::withoutVerifying()
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://api.shasta.trongrid.io/wallet/getaccount', [
                'address' => $address,
                'visible' => true,
            ])
            ->throw()
            ->json();


        return $response['account_name'];
    }

    public function getAccountResource(string $address)
    {
        $response = Http::withoutVerifying()
            ->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ])->post('https://api.shasta.trongrid.io/wallet/getaccountresource', [
                'address' => $address,
                'visible' => true,
            ])
            ->throw()
            ->json();

        dd($response);

        return $response['result'];
    }
}
