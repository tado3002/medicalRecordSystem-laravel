<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocterController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CustomAuthenticate;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', CustomAuthenticate::class])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('search', [UsersController::class, 'search'])->middleware('role:ADMIN');

        Route::middleware('role:ADMIN')->group(function () {
            Route::get('{id}', [UsersController::class, 'findOne'])
                ->where(['id' => '[0-9]+']);
            Route::put('{id}', [UsersController::class, 'update'])
                ->where(['id' => '[0-9]+']);
            Route::delete('{id}', [UsersController::class, 'delete'])
                ->where(['id' => '[0-9]+']);
        });

        Route::get('profile', [UsersController::class, 'profile']);
    });

    Route::prefix('users')->group(function () {
        Route::post('{id}/docters', [DocterController::class, 'create'])
            ->where(['id' => '[0-9]+'])
        ;
    })->middleware('role:ADMIN');

    Route::prefix('docters')->group(function () {
        Route::post('', [DocterController::class, 'create'])
            ->middleware('role:ADMIN');
        Route::get('', [DocterController::class, 'findAll']);
        Route::get('search', [DocterController::class, 'search']);
        Route::get('{id}', [DocterController::class, 'findOne'])
            ->where(['id' => '[0-9]+']);
        Route::put('{id}', [DocterController::class, 'update'])
            ->middleware('role:ADMIN')
            ->where(['id' => '[0-9]+']);
        Route::delete('{id}', [DocterController::class, 'delete'])
            ->middleware('role:ADMIN')
            ->where(['id' => '[0-9]+']);
    });
    Route::post('patients', [PatientController::class, 'create']);
    Route::get('/patients/{id}', [PatientController::class, 'findOne'])
        ->where(['id' => '[0-9]+']);
    Route::put('/patients/{id}', [PatientController::class, 'update'])
        ->where(['id' => '[0-9]+']);
    Route::delete('/patients/{id}', [PatientController::class, 'delete'])
        ->where(['id' => '[0-9]+']);
    Route::get('patients/search', [PatientController::class, 'search']);


    Route::post('appointments', [AppointmentController::class, 'create']);
    Route::put('appointments/{id}', [AppointmentController::class, 'update'])
        ->where(['id' => '[0-9]+']);
    Route::delete('appointments/{id}', [AppointmentController::class, 'delete'])
        ->where(['id' => '[0-9]+']);
    Route::get('appointments/search', [AppointmentController::class, 'search']);

    Route::prefix('medical_records')->group(function () {
        Route::post('', [MedicalRecordController::class, 'create']);
        Route::get('{id}', [MedicalRecordController::class, 'findOne'])
            ->where(['id' => '[0-9]+']);
        Route::put('{id}', [MedicalRecordController::class, 'update'])
            ->where(['id' => '[0-9]+']);
        Route::delete('{id}', [MedicalRecordController::class, 'delete'])
            ->where(['id' => '[0-9]+']);
    });
});
