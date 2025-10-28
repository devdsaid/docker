<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BalanceController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->middleware('api')->controller(BalanceController::class)->group(function () {
    Route::get('/balance/{user_id}', 'getBalance');
    Route::post('/deposit', 'deposit');
    Route::post('/withdraw', 'withdraw');
    Route::post('/transfer', 'transfer');
});

