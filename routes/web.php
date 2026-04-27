<?php

use App\Http\Controllers\AdminRideController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RiderDashboardController;
use App\Http\Controllers\RideRequestController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        return redirect()->route(match ($request->user()->role) {
            'admin' => 'admin.dashboard',
            'rider' => 'rider.dashboard',
            default => 'customer.dashboard',
        });
    })->name('dashboard');

    Route::middleware('role:customer')->group(function () {
        Route::get('/customer/dashboard', [RideRequestController::class, 'dashboard'])->name('customer.dashboard');
        Route::get('/rides', [RideRequestController::class, 'index'])->name('rides.index');
        Route::get('/rides/create', [RideRequestController::class, 'create'])->name('rides.create');
        Route::post('/rides', [RideRequestController::class, 'store'])->name('rides.store');
        Route::get('/rides/{rideRequest}', [RideRequestController::class, 'show'])->name('rides.show');
        Route::post('/rides/{rideRequest}/cancel', [RideRequestController::class, 'cancel'])->name('rides.cancel');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminRideController::class, 'dashboard'])->name('dashboard');
        Route::get('/rides', [AdminRideController::class, 'index'])->name('rides.index');
        Route::get('/rides/{rideRequest}', [AdminRideController::class, 'show'])->name('rides.show');
        Route::post('/rides/{rideRequest}/assign', [AdminRideController::class, 'assignRider'])->name('rides.assign');

        Route::get('/customers', [UserManagementController::class, 'customers'])->name('customers.index');
        Route::get('/riders', [UserManagementController::class, 'riders'])->name('riders.index');
        Route::get('/riders/create', [UserManagementController::class, 'createRider'])->name('riders.create');
        Route::post('/riders', [UserManagementController::class, 'storeRider'])->name('riders.store');
        Route::get('/riders/{user}/edit', [UserManagementController::class, 'editRider'])->name('riders.edit');
        Route::put('/riders/{user}', [UserManagementController::class, 'updateRider'])->name('riders.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'deleteUser'])->name('users.delete');
    });

    Route::middleware('role:rider')->prefix('rider')->name('rider.')->group(function () {
        Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');
        Route::post('/rides/{rideRequest}/accept', [RiderDashboardController::class, 'acceptRide'])->name('rides.accept');
        Route::post('/rides/{rideRequest}/start', [RiderDashboardController::class, 'startRide'])->name('rides.start');
        Route::post('/rides/{rideRequest}/complete', [RiderDashboardController::class, 'completeRide'])->name('rides.complete');
    });
});
