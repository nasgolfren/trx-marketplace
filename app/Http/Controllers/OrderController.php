<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderPostRequest;
use App\Http\Requests\SellOrderPostRequest;
use App\Http\Services\GeneralService;
use App\Models\Order;
use App\Models\OrderSell;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function __construct(
        protected GeneralService $genService,
    ) {}


    public function store(OrderPostRequest $request)
    {
        try {
            $data = $request->safe()->all();

            if (!$this->genService->isAddressCorrect($data['source_address'])) {
                return back()->withErrors(['source_address' => 'Tron address is not valid'])->withInput();
            }

            $data['show_at'] = now()->tz(config('app.timezone'))->addMinutes(15);
            $data['target_address'] = config('app.targetAddress');

            Order::create($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }

    public function storeSellOrder(SellOrderPostRequest $request)
    {
        try {
            $data = $request->safe()->all();

            if (!$this->genService->isAddressCorrect($data['payout_target_address'])) {
                return back()->withErrors(['payout_target_address' => 'Tron address is not valid'])->withInput();
            }

            $data['order_id'] = Order::where('unique_id', $data['unique_id'])->first()->id;

            OrderSell::create($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }
}
