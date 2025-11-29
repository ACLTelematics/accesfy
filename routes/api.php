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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// SuperUser Routes
Route::apiResource('super-users', SuperUserController::class);

// GymOwner Routes
Route::apiResource('gym-owners', GymOwnerController::class);

// Staff Routes
Route::apiResource('staff', StaffController::class);

// Client Routes
Route::apiResource('clients', ClientController::class);

// Membership Routes
Route::apiResource('memberships', MembershipController::class);

// Attendance Routes
Route::apiResource('attendances', AttendanceController::class);
Route::post('attendances/check-in', [AttendanceController::class, 'checkIn']);
Route::post('attendances/{attendance}/check-out', [AttendanceController::class, 'checkOut']);

// Payment Routes
Route::apiResource('payments', PaymentController::class);
Route::get('payments/client/{clientId}', [PaymentController::class, 'getByClient']);
Route::get('payments/gym-owner/{gymOwnerId}', [PaymentController::class, 'getByGymOwner']);

// Backup Routes
Route::apiResource('backups', BackupController::class);
Route::post('backups/{backup}/apply', [BackupController::class, 'apply']);
Route::get('backups/gym-owner/{gymOwnerId}', [BackupController::class, 'getByGymOwner']);