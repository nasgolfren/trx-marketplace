<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderPostRequest;
use App\Http\Services\GeneralService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrderController extends Controller
{

    public function __construct(
        protected GeneralService $genService,
    ) {}


    public function order(OrderPostRequest $request)
    {
        $data = $request->safe()->all();

        if (!$this->genService->isAddressCorrect($data['address'])) {
            return back()->withErrors(['address' => 'Tron address is not valid'])->withInput();
        }

        Order::firstOrCreate(
            $data,
            $data
        );
    }
}
