<?php

use App\Http\Controllers\BalanceInquiryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('test-api', function (Request $request) {
    return response()->json([
        'status' => 200,
        'message' => 'Hello World!',
        'data' => [
            'nama' => 'Mon',
            'umur' => 23,
        ],
    ]);
});

Route::post('/balance-inquiry', [BalanceInquiryController::class, 'balanceInquiry']);
