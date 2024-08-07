<?php

use App\Http\Controllers\Api\TaskController;
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
    Route::post('task/create', [TaskController::class, 'create']);
    Route::post('task/edit/{id}', [TaskController::class, 'edit']);
    Route::post('task/delete/{id}', [TaskController::class, 'destroy']);

    Route::post('task/change-status/{id}', [TaskController::class, 'changeStatus']);
});
