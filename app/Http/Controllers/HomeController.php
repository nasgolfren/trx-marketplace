<?php

namespace App\Http\Controllers;

use App\Http\Requests\WalletInfoPostRequest;
use App\Http\Services\GeneralService;
use App\Models\Order;
use Illuminate\Http\Request;
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
                'orders' => Order::where('show_at', '<', now())->orderBy('created_at', 'desc')->get(),
                'resources' => config('app.resources'),
                'formConfig' => config('app.formConfig'),
                'targetAddress' => config('app.targetAddress'),
                'tronscanTransaction' => config('app.tronscanTransaction'),
                'tronscanAdress' => config('app.tronscanAdress'),
                'connectedWallet' => session('connectedWallet'),
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }
}
