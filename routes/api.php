<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'authenticate']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('jwt.verify');
    Route::get('get-user', [AuthController::class, 'get_user'])->middleware('jwt.verify');
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::apiResource('products',ProductController::class);
});
