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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Auth\SuperUserAuthController;
use App\Http\Controllers\Auth\GymOwnerAuthController;
use App\Http\Controllers\Auth\StaffAuthController;

// ============================================================================
// AUTH ROUTES - PUBLIC
// ============================================================================
Route::post('auth/super-user/login', [SuperUserAuthController::class, 'login']);
Route::post('auth/gym-owner/login', [GymOwnerAuthController::class, 'login']);
Route::post('auth/staff/login', [StaffAuthController::class, 'login']);

// ============================================================================
// AUTH ROUTES - PROTECTED
// ============================================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/super-user/logout', [SuperUserAuthController::class, 'logout']);
    Route::get('auth/super-user/me', [SuperUserAuthController::class, 'me']);

    Route::post('auth/gym-owner/logout', [GymOwnerAuthController::class, 'logout']);
    Route::get('auth/gym-owner/me', [GymOwnerAuthController::class, 'me']);

    Route::post('auth/staff/logout', [StaffAuthController::class, 'logout']);
    Route::get('auth/staff/me', [StaffAuthController::class, 'me']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// ============================================================================
// SUPERUSER ROUTES
// ============================================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('super-users', SuperUserController::class);
});

// ============================================================================
// GYM OWNER ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('gym-owners', GymOwnerController::class);
});

// ============================================================================
// STAFF ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::apiResource('staff', StaffController::class);
    Route::post('staff/{staff}/reset-password', [StaffController::class, 'resetPassword']);
    Route::get('gym-owner/staff', [StaffController::class, 'getByGymOwner']);
});

// ============================================================================
// CLIENT ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::get('clients/search', [ClientController::class, 'search']);
    Route::apiResource('clients', ClientController::class);
});

// ============================================================================
// MEMBERSHIP ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::get('gym-owner/memberships', [MembershipController::class, 'getByGymOwner']);
    Route::post('memberships/{membership}/toggle-active', [MembershipController::class, 'toggleActive']);
    Route::apiResource('memberships', MembershipController::class);
});

// ============================================================================
// ATTENDANCE ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::post('attendances/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('attendances/check-in-biometric', [AttendanceController::class, 'checkInBiometric']);
    Route::post('attendances/{attendance}/check-out', [AttendanceController::class, 'checkOut']);
    Route::apiResource('attendances', AttendanceController::class);
});

// ============================================================================
// PAYMENT ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::get('payments/client/{clientId}', [PaymentController::class, 'getByClient']);
    Route::get('payments/gym-owner/{gymOwnerId}', [PaymentController::class, 'getByGymOwner']);
    Route::apiResource('payments', PaymentController::class);
});

// ============================================================================
// BACKUP ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::post('backups/{backup}/apply', [BackupController::class, 'apply']);
    Route::get('backups/gym-owner/{gymOwnerId}', [BackupController::class, 'getByGymOwner']);
    Route::apiResource('backups', BackupController::class);
});

// ============================================================================
// DASHBOARD ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/recent-activity', [DashboardController::class, 'recentActivity']);
    Route::get('dashboard/expiring-members', [DashboardController::class, 'expiringMembers']);
    Route::get('dashboard/membership-distribution', [DashboardController::class, 'membershipDistribution']);
    Route::get('dashboard/peak-hours', [DashboardController::class, 'peakHours']);
    Route::get('dashboard/gender-distribution', [DashboardController::class, 'genderDistribution']);
});

// ============================================================================
// NOTIFICATION ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});

// ============================================================================
// SETTINGS ROUTES
// ============================================================================
Route::middleware(['auth:sanctum', 'gym.expiration'])->group(function () {
    Route::get('settings', [SettingsController::class, 'index']);
    Route::post('settings', [SettingsController::class, 'update']);
    Route::get('settings/{key}', [SettingsController::class, 'get']);
    Route::post('settings/logo', [SettingsController::class, 'uploadLogo']);
});
