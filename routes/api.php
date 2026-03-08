<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\SongApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/save-user', [SongApiController::class, 'saveUser']);
Route::post('/save-song', [SongApiController::class, 'saveSong']);
Route::post('/get-user', [SongApiController::class, 'getUser']);
Route::post('/get-songs-by-filter', [SongApiController::class, 'getSongsByFilter']);
Route::get('/get-random-songs', [SongApiController::class, 'getRandomSongs']);
