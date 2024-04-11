<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderPostRequest;
use App\Http\Services\GeneralService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use App\Models\Order;

class OrderController extends Controller
{

    public function __construct(
        protected GeneralService $genService,
    ) {}


    public function order(OrderPostRequest $request)
    {
        $data = $request->safe()->all();

        if (!$this->genService->isAddressCorrect($data['target_address'])) {
            return back()->withErrors(['target_address' => 'Tron address is not valid'])->withInput();
        }

        Order::create($data);
    }
}
