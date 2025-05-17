<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CustomAuthenticate;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');

Route::middleware([EnsureTokenIsValid::class, CustomAuthenticate::class])->group(function () {
    Route::get('/users/profile', [UsersController::class, 'profile']);
});
