<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-16

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


/**
 * ------------------------------------------------------------
 * Public Routes
 * ------------------------------------------------------------
 */
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

/**
 * ------------------------------------------------------------
 * Protected Routes (Require Sanctum Token)
 * ------------------------------------------------------------
 * Use Bearer token header:
 * Authorization: Bearer <token>
 */
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});
