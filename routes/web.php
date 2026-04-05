<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\SongModuleController;
use App\Http\Controllers\Admin\SongCoverController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [SongModuleController::class, 'dashboard'])->name('dashboard');

    Route::get('/admin/app-users', [SongModuleController::class, 'appUsers'])->name('admin.app_users');
    Route::get('/admin/songs', [SongModuleController::class, 'songs'])->name('admin.songs');
    Route::get('/admin/app-users/{id}/songs', [SongModuleController::class, 'userSongs'])->name('admin.user_songs');

    Route::get('/admin/song-covers', [SongCoverController::class, 'index'])->name('admin.song_covers');
    Route::post('/admin/song-covers', [SongCoverController::class, 'store'])->name('admin.song_covers.store');
    Route::post('/admin/song-covers/{id}', [SongCoverController::class, 'update'])->name('admin.song_covers.update');
    Route::delete('/admin/song-covers/{id}', [SongCoverController::class, 'destroy'])->name('admin.song_covers.destroy');

    Route::get('/admin/api-list', function () {
        return view('admin.api_list.index');
    })->name('admin.api_list');
});