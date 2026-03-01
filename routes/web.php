<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\Admin\SongModuleController;

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [SongModuleController::class, 'dashboard'])->name('dashboard');

    Route::get('/admin/app-users', [SongModuleController::class, 'appUsers'])->name('admin.app_users');
    Route::get('/admin/songs', [SongModuleController::class, 'songs'])->name('admin.songs');
    Route::get('/admin/app-users/{id}/songs', [SongModuleController::class, 'userSongs'])->name('admin.user_songs');

    Route::get('/admin/api-list', function () {
        return view('admin.api_list.index');
    })->name('admin.api_list');
});