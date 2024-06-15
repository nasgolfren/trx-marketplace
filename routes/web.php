<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WalletController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['cors', 'access-log'])->group(function () {
    Route::get('/', [HomeController::class, 'show']);

    Route::group(['prefix' => 'wallet-info'], function () {
        Route::post('/', [WalletController::class, 'store']);
        Route::delete('/', [WalletController::class, 'logout']);
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::post('/', [OrderController::class, 'store']);
        Route::post('/{unique_id}', [OrderController::class, 'storeSellOrder']);
    });
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
