<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderPostRequest;
use App\Http\Services\GeneralService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class HomeController extends Controller
{

    public function __construct(
        protected GeneralService $genService,
    ) {}


    public function index(Request $request)
    {
        return Inertia::render('Welcome', [
            'orders' => Order::orderBy('created_at', 'desc')->get(),
            'resources' => config('app.resources'),
            'formConfig' => config('app.formConfig'),
        ]);
    }
}
