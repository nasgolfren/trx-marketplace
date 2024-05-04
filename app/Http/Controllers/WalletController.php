<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletInfoPostRequest;
use App\Http\Services\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{

    public function __construct(
        protected GeneralService $genService,
    ) {}


    public function save(WalletInfoPostRequest $request)
    {
        try {
            $data = $request->safe()->all();

            if (!$this->genService->isAddressCorrect($data['wallet_address'])) {
                return throw new \Exception("Wallet address is not valid!");
            }

            $response = Http::withoutVerifying()
                ->get('https://apilist.tronscanapi.com/api/accountv2?address=' . $data['wallet_address'])
                ->throw()
                ->json();

            if (empty($response)) {
                return throw new \Exception("Tronscanapi is not working, please try to connect later!");
            }

            $response['wallet_balance'] = round((Arr::get($response, 'balance', 0) / 1000000), 2);
            $response['wallet_staked_trx'] = round((Arr::get($response, 'totalFrozenV2', 0) / 1000000), 2);
            $response['wallet_delegated_energy'] = round(($response['wallet_staked_trx'] * Arr::get($response, 'energyCost', 0)), 2);

            session(['connectedWallet' => $response]);

        } catch (\Throwable $e) {
            Log::error($e);
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request)
    {
        try {
            session()->forget('connectedWallet');

            return back();

        } catch (\Throwable $e) {
            Log::error($e);
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }
}
