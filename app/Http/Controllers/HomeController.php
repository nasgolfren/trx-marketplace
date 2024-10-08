<?php

namespace App\Http\Controllers;

use App\Http\Services\GeneralService;
use App\Models\Order;
use App\Models\OrderSell;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class HomeController extends Controller
{

    public function __construct(
        protected GeneralService $genService,
    ) {}


    public function show(Request $request)
    {
        try {
            return Inertia::render('Welcome', [
                'orders' => Order::where('show_at', '<', now())
                    ->orderBy('is_fullfilled')
                    ->orderBy('created_at', 'desc')
                    ->get(),
                'myOrders' => (session('connectedWallet') == null)
                    ? []
                    : Order::where('source_address', Arr::get(session('connectedWallet'), 'address'))
                        ->where('show_at', '<', now())
                        ->orderBy('created_at', 'desc')
                        ->get(),
                'myReceipts' => (session('connectedWallet') == null)
                    ? []
                    : OrderSell::where('payout_target_address', Arr::get(session('connectedWallet'), 'address'))
                        ->orderBy('created_at', 'desc')
                        ->with(['order'])
                        ->get(),
                'resources' => config('app.resources'),
                'formConfig' => config('app.formConfig'),
                'targetAddress' => config('app.targetAddress'),
                'tronscanTransaction' => config('app.tronscanTransaction'),
                'tronscanAdress' => config('app.tronscanAdress'),
                'connectedWallet' => session('connectedWallet'),
                'reward' => config('app.reward'),
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }
}
