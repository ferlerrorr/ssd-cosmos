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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/manual-soa', [App\Http\Controllers\ManualSoaController::class, 'store']);


Route::post('/esoa-bdo', [App\Http\Controllers\ESoaBdoController::class, 'store']);


Route::post('/esoa-rbank', [App\Http\Controllers\ESoaRbankController::class, 'store']);
