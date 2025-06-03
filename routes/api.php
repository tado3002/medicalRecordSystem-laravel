<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocterController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CustomAuthenticate;
use Illuminate\Support\Facades\Route;

// auth route
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', CustomAuthenticate::class])->group(function () {

    // users route
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

    // docters route
    Route::prefix('docters')->group(function () {
        Route::post('', [DocterController::class, 'create'])
            ->middleware('role:ADMIN');
        Route::get('{id}', [DocterController::class, 'findOne'])
            ->where(['id' => '[0-9]+']);
        Route::put('{id}', [DocterController::class, 'update'])
            ->middleware('role:ADMIN')
            ->where(['id' => '[0-9]+']);
        Route::delete('{id}', [DocterController::class, 'delete'])
            ->middleware('role:ADMIN')
            ->where(['id' => '[0-9]+']);
        Route::get('search', [DocterController::class, 'search']);
    });

    // patients route
    Route::prefix('patients')->group(function () {
        Route::post('', [PatientController::class, 'create']);
        Route::get('{id}', [PatientController::class, 'findOne'])
            ->where(['id' => '[0-9]+']);
        Route::put('{id}', [PatientController::class, 'update'])
            ->where(['id' => '[0-9]+']);
        Route::delete('{id}', [PatientController::class, 'delete'])
            ->where(['id' => '[0-9]+']);
        Route::get('search', [PatientController::class, 'search']);
    });

    // appointment route
    Route::prefix('appointments')->group(function () {
        Route::post('', [AppointmentController::class, 'create']);
        Route::get('{id}', [AppointmentController::class, 'findOne']);
        Route::put('{id}', [AppointmentController::class, 'update'])
            ->where(['id' => '[0-9]+']);
        Route::delete('{id}', [AppointmentController::class, 'delete'])
            ->where(['id' => '[0-9]+']);
        Route::get('search', [AppointmentController::class, 'search']);
    });

    // medical_records route
    Route::prefix('medical_records')->group(function () {
        Route::post('', [MedicalRecordController::class, 'create'])
            ->middleware('role:DOCTER');
        Route::get('{id}', [MedicalRecordController::class, 'findOne'])
            ->where(['id' => '[0-9]+']);
        Route::put('{id}', [MedicalRecordController::class, 'update'])
            ->where(['id' => '[0-9]+'])
            ->middleware('role:DOCTER');
        Route::delete('{id}', [MedicalRecordController::class, 'delete'])
            ->where(['id' => '[0-9]+'])
            ->middleware('role:DOCTER');
    });

    // prescriptions route
    Route::prefix('prescriptions')->group(function () {
        Route::get('', [PrescriptionController::class, 'findAll']);
        Route::post('', [PrescriptionController::class, 'create']);
        Route::get('{id}', [PrescriptionController::class, 'findOne'])
            ->where(['id' => '[0-9]+']);
        Route::put('{id}', [PrescriptionController::class, 'update'])
            ->where(['id' => '[0-9]+']);
        Route::delete('{id}', [PrescriptionController::class, 'delete'])
            ->where(['id' => '[0-9]+']);
    });
});
