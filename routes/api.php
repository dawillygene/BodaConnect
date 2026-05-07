<?php

use App\Http\Controllers\Api\AdminMonitoringController;
use App\Http\Controllers\Api\AdminRideController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerRideController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\RiderRideController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:api');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:api');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/dashboard', DashboardController::class);

    Route::middleware('role:customer')->group(function (): void {
        Route::get('/rides', [CustomerRideController::class, 'index']);
        Route::post('/rides', [CustomerRideController::class, 'store']);
        Route::get('/rides/{rideRequest}', [CustomerRideController::class, 'show']);
        Route::post('/rides/{rideRequest}/cancel', [CustomerRideController::class, 'cancel']);
    });

    Route::middleware('role:admin')->prefix('admin')->group(function (): void {
        Route::get('/monitoring', AdminMonitoringController::class);
        Route::get('/rides', [AdminRideController::class, 'index']);
        Route::get('/rides/{rideRequest}', [AdminRideController::class, 'show']);
        Route::post('/rides/{rideRequest}/assign', [AdminRideController::class, 'assign']);

        Route::get('/customers', [AdminUserController::class, 'customers']);
        Route::get('/riders', [AdminUserController::class, 'riders']);
        Route::get('/riders/{user}', [AdminUserController::class, 'showRider']);
        Route::post('/riders', [AdminUserController::class, 'storeRider']);
        Route::put('/riders/{user}', [AdminUserController::class, 'updateRider']);
        Route::delete('/users/{user}', [AdminUserController::class, 'deleteUser']);
    });

    Route::middleware('role:rider')->prefix('rider')->group(function (): void {
        Route::get('/rides', [RiderRideController::class, 'index']);
        Route::post('/rides/{rideRequest}/accept', [RiderRideController::class, 'accept']);
        Route::post('/rides/{rideRequest}/start', [RiderRideController::class, 'start']);
        Route::post('/rides/{rideRequest}/complete', [RiderRideController::class, 'complete']);
    });
});
