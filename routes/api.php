<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperUserController;
use App\Http\Controllers\GymOwnerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\Auth\SuperUserAuthController;
use App\Http\Controllers\Auth\GymOwnerAuthController;
use App\Http\Controllers\Auth\StaffAuthController;

// Auth Routes - Public
Route::post('auth/super-user/login', [SuperUserAuthController::class, 'login']);
Route::post('auth/gym-owner/login', [GymOwnerAuthController::class, 'login']);
Route::post('auth/staff/login', [StaffAuthController::class, 'login']);

// Auth Routes - Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/super-user/logout', [SuperUserAuthController::class, 'logout']);
    Route::get('auth/super-user/me', [SuperUserAuthController::class, 'me']);
    
    Route::post('auth/gym-owner/logout', [GymOwnerAuthController::class, 'logout']);
    Route::get('auth/gym-owner/me', [GymOwnerAuthController::class, 'me']);
    
    Route::post('auth/staff/logout', [StaffAuthController::class, 'logout']);
    Route::get('auth/staff/me', [StaffAuthController::class, 'me']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// SuperUser Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('super-users', SuperUserController::class);
});

// GymOwner Routes
    Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('gym-owners', GymOwnerController::class);
});

// Staff Routes
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('staff', StaffController::class);
});

// Client Routes
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('clients', ClientController::class);
});

// Membership Routes
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('memberships', MembershipController::class);
});

// Attendance Routes
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('attendances', AttendanceController::class);
    Route::post('attendances/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('attendances/{attendance}/check-out', [AttendanceController::class, 'checkOut']);
});

// Payment Routes
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('payments', PaymentController::class);
    Route::get('payments/client/{clientId}', [PaymentController::class, 'getByClient']);
    Route::get('payments/gym-owner/{gymOwnerId}', [PaymentController::class, 'getByGymOwner']);
});


// Backup Routes
    Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('backups', BackupController::class);
    Route::post('backups/{backup}/apply', [BackupController::class, 'apply']);
    Route::get('backups/gym-owner/{gymOwnerId}', [BackupController::class, 'getByGymOwner']);
});