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


Route::post('/signup', [UserController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/wallet/sendMoney', [WalletController::class, 'sendMoney'])->middleware('auth.native');
Route::get('/wallet/transactions/{walletId}', [WalletController::class, 'transactions'])->middleware('auth.native');
