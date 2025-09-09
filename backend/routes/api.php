<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\StudentManagementController; // Impor controller di atas

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === Rute Publik (Untuk Login) ===
Route::post('/student-login', [AuthController::class, 'studentLogin']);
Route::post('/guru/login', [AuthController::class, 'guruLogin']);

// === Rute Terproteksi (Membutuhkan Token Autentikasi) ===
Route::middleware('auth:sanctum')->group(function () {
    
    // Rute umum untuk user yang terautentikasi (Siswa & Guru)
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rute untuk Laporan (Reports)
    Route::get('/reports', [ReportController::class, 'index']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::put('/reports/{report}/accept', [ReportController::class, 'accept']);
    Route::put('/reports/{report}/schedule', [ReportController::class, 'schedule']);
    Route::put('/reports/{report}/complete', [ReportController::class, 'complete']);

    // Rute untuk Pesan (Chats)
    Route::get('/reports/{report}/chats', [ChatController::class, 'index']);
    Route::post('/reports/{report}/chats', [ChatController::class, 'store']);


    // === Rute Khusus untuk Guru/Konselor ===
    Route::prefix('guru')->group(function () {
        // Rute untuk Manajemen Siswa oleh Guru
        Route::get('/students', [StudentManagementController::class, 'index']);       // GET /api/guru/students
        Route::post('/students', [StudentManagementController::class, 'store']);      // POST /api/guru/students
        Route::get('/students/{id}', [StudentManagementController::class, 'show']);     // GET /api/guru/students/{id}
        Route::put('/students/{id}', [StudentManagementController::class, 'update']);     // PUT /api/guru/students/{id}
        Route::delete('/students/{id}', [StudentManagementController::class, 'destroy']); // DELETE /api/guru/students/{id}
    });

});

