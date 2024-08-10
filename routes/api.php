<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterLevelController;

Route::get('/water-level', [WaterLevelController::class, 'getWaterLevel']);
Route::post('/water-level', [WaterLevelController::class, 'store']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
