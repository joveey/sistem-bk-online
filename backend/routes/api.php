<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === Rute Publik (Autentikasi) ===
Route::post('/student-login', [AuthController::class, 'studentLogin']);
Route::post('/guru/login', [AuthController::class, 'guruLogin']);

// === Rute Terproteksi (Membutuhkan Token) ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/students', [App\Http\Controllers\Api\StudentManagementController::class, 'store']); // Membuat siswa baru
    Route::get('/students', [App\Http\Controllers\Api\StudentManagementController::class, 'index']); // Melihat daftar siswa
    Route::put('/students/{id}', [App\Http\Controllers\Api\StudentManagementController::class, 'update']); // Update siswa
    Route::delete('/students/{id}', [App\Http\Controllers\Api\StudentManagementController::class, 'destroy']); // Hapus siswa
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::put('/reports/{report}/accept', [ReportController::class, 'accept']);
    Route::put('/reports/{report}/schedule', [ReportController::class, 'schedule']);
    Route::put('/reports/{report}/complete', [ReportController::class, 'complete']);

    // Chats
    Route::get('/reports/{report}/chats', [ChatController::class, 'index']);
    Route::post('/reports/{report}/chats', [ChatController::class, 'store']);
});
