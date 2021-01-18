<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\NewsController;
use App\Http\Controllers\V1\RajaOngkirController;
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
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function(){

    Route::prefix('/user')->group(function(){
        Route::get('/profile', [AuthController::class, 'getUserProfile']);
        Route::get('/all', [AuthController::class, 'showAll']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::prefix('/post')->group(function(){
            Route::post('/', [NewsController::class, 'create']);
            Route::get('/', [NewsController::class, 'getAll']);
            Route::get('/show/{id}', [NewsController::class, 'show']);
            Route::put('/update/{id}', [NewsController::class, 'update']);
            Route::delete('/delete/{id}', [NewsController::class, 'destroy']);
        });
        
    });

    Route::prefix('/raja-ongkir')->group(function(){
        Route::get('/province', [RajaOngkirController::class, 'searchProvinces']);
        Route::get('/city', [RajaOngkirController::class, 'searchCities']);
        Route::post('/check-cost', [RajaOngkirController::class, 'checkCost']);
    });
});
