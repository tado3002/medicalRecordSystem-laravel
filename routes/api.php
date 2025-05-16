<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::post('/users/register', [UsersController::class, 'register']);
Route::post('/users/login', [UsersController::class, 'login']);
