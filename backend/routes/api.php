<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// مسیرهای عمومی (بدون نیاز به لاگین)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// مسیرهای نیازمند لاگین (محافظت‌شده با Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});