<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
//use App\Http\Controllers\StatController;




Route::middleware(['nativeAPI'])->group(function () {
    /* I don't use route resources unless it's binded by the SRS  */
    Route::post('/wallet/sendMoney', [WalletController::class, 'sendMoney']);
    Route::get('/wallet/transactions/{walletId}', [WalletController::class, 'transactions']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});


Route::post('/signup', [UserController::class, 'signup']);
Route::post('/auth/login', [AuthController::class, 'login']);









