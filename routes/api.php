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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\FRQAPIController;
Route::prefix('frq')->group(function () {
    Route::get('getList' , [FRQAPIController::class,'getList']);
    Route::get('getList/pk' , [FRQAPIController::class,'getList_pk']);
    Route::get('getList/test' , [FRQAPIController::class,'getList_test']);
    Route::get('order/{InquiryID}' , [FRQAPIController::class,'getOrder']);
});
