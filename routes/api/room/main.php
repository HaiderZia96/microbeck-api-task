<?php

use App\Http\Controllers\Api\RoomController;
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


Route::group(['prefix' => 'animal-rescue-app', 'middleware' => ['auth:sanctum']], function () {
    Route::post('room/create', [RoomController::class, 'create']);
    Route::put('room/edit/{id}', [RoomController::class, 'edit']);
    Route::delete('room/delete/{id}', [RoomController::class, 'destroy']);
});
