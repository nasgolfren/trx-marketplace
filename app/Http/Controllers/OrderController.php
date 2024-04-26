<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderPostRequest;
use App\Http\Services\GeneralService;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    public function __construct(
        protected GeneralService $genService,
    ) {}


    public function order(OrderPostRequest $request)
    {
        try {
            $data = $request->safe()->all();

            if (!$this->genService->isAddressCorrect($data['source_address'])) {
                return back()->withErrors(['source_address' => 'Tron address is not valid'])->withInput();
            }

            Order::create($data);
        } catch (\Throwable $e) {
            Log::error($e);
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }
}
