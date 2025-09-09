<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    // Mengambil semua chat dalam sebuah laporan
    public function index(Request $request, Report $report)
    {
        $user = $request->user();

        // Otorisasi: Siswa hanya bisa lihat chat dari laporannya sendiri
        if ($user instanceof Student && $report->student_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        // 'sender' adalah relasi polimorfik yang kita definisikan di model Chat
        $chats = $report->chats()->with('sender')->latest()->get();

        return response()->json($chats);
    }

    // Mengirim pesan baru
    public function store(Request $request, Report $report)
    {
        $user = $request->user();

        // Otorisasi: Pastikan user berhak mengakses chat ini
        if ($user instanceof Student && $report->student_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($user instanceof \App\Models\User && $report->counselor_id !== $user->id) {
             return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $chat = $report->chats()->create([
            'message' => $request->message,
            'sender_id' => $user->id,
            'sender_type' => get_class($user) // Otomatis mengisi tipe model (App\Models\User atau App\Models\Student)
        ]);

        return response()->json($chat->load('sender'), 201);
    }
}
