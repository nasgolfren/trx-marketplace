<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletInfoPostRequest;
use App\Http\Services\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

            $data['wallet_balance'] = round((Arr::get($data, 'account.balance', 0) / 1000000), 2);
            $data['wallet_bandwith'] = Arr::get($data, 'account_resource.freeNetLimit', 0);
            $data['wallet_energy'] = Arr::get($data, 'account_resource.EnergyLimit', 0);

            session(['connectedWallet' => $data]);

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
