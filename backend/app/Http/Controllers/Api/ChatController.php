<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Report;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Menampilkan semua chat untuk sebuah laporan.
     */
    public function index(Request $request, Report $report)
    {
        // Otorisasi: Pastikan user bisa melihat laporan ini terlebih dahulu
        $user = $request->user();
        $isOwner = ($user instanceof \App\Models\Student && $report->student_id == $user->id);
        $isCounselor = ($user instanceof \App\Models\User && $report->counselor_id == $user->id);

        if (!$isOwner && !$isCounselor) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($report->chats()->orderBy('created_at', 'asc')->get());
    }

    /**
     * Menyimpan pesan chat baru.
     */
    public function store(Request $request, Report $report)
    {
        // --- LOGIKA KUNCI ADA DI SINI ---

        // 1. Validasi input
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // 2. Otorisasi: Siapa yang boleh mengirim pesan?
        $user = $request->user();
        $isOwner = ($user instanceof \App\Models\Student && $report->student_id == $user->id);
        $isCounselor = ($user instanceof \App\Models\User && $report->counselor_id == $user->id);

        // Tolak jika user bukan pemilik laporan ATAU konselor yang menanganinya
        if (!$isOwner && !$isCounselor) {
            return response()->json(['message' => 'Anda tidak diizinkan mengirim pesan di laporan ini.'], 403);
        }

        // 3. Buat dan simpan chat
        $chat = new Chat();
        $chat->report_id = $report->id;
        $chat->message = $validated['message'];
        
        // Gunakan relasi polimorfik untuk menyimpan info pengirim
        $chat->sender()->associate($user);

        $chat->save();

        return response()->json($chat, 201);
    }
}

