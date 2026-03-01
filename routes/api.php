<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SongApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/save-user', [SongApiController::class, 'saveUser']);
Route::post('/save-song', [SongApiController::class, 'saveSong']);
