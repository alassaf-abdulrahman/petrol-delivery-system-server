<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', UserController::class . '@login');
Route::post('/register', UserController::class . '@createUserAccount');
Route::put('/update-profile', UserController::class . '@updateUserAccount');

Route::prefix('customer')->group(function () {
    Route::post('/place-fuel-order', CustomerController::class . '@placeFuelOrder');
    Route::get('/tarck-fuel-order', CustomerController::class . '@trackFuelOrder');
    Route::post('/make-payment', CustomerController::class . '@makePayment');
    Route::post('/view-orders', CustomerController::class . '@getOrders');
});

Route::prefix('driver')->group(function () {
    Route::post('/fuel-orders', DriverController::class . '@getFuelOrders');
    Route::put('/accept-fuel-order', DriverController::class . '@acceptFuelOrder');
    Route::put('/confirm-delivery', DriverController::class . '@confirmFuelDelivery');
});

Route::prefix('admin')->group(function () {
    Route::get('/fuel-orders', AdminController::class . '@getFuelsOrdersList');
    Route::put('/assign-driver', AdminController::class . '@assignDriverToFuelOrder');
    Route::put('/reject-order', AdminController::class . '@rejectFuelOrder');
    Route::post('/generate-report', AdminController::class . '@generateMonthlyReport');
    Route::get('/drivers', AdminController::class . '@getDrivers');
});
