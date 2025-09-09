<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    /**
     * Mengambil daftar laporan berdasarkan role user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Report::with('student:id,name'); // Eager load nama siswa

        // Cek tipe model dari user yang terautentikasi
        if ($user instanceof \App\Models\Student) {
            // Jika user adalah siswa, filter berdasarkan student_id
            $query->where('student_id', $user->id);
        }
        // Jika bukan siswa (berarti konselor), tidak perlu filter tambahan,
        // sehingga akan mengambil semua laporan.

        $reports = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['data' => $reports]);
    }

    /**
     * Membuat laporan baru (hanya siswa)
     */
    public function store(Request $request)
    {
        if (!$request->user() instanceof Student) {
            return response()->json(['message' => 'Hanya siswa yang dapat membuat laporan.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:online,offline',
            'is_anonymous' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        if (!$data['is_anonymous']) {
            $data['student_id'] = $request->user()->id;
        }

        $report = Report::create($data);

        return response()->json(['data' => $report], 201);
    }

    /**
     * Menampilkan detail satu laporan
     */
    public function show(Request $request, Report $report)
    {
        $user = $request->user();

        // Otorisasi: Siswa hanya bisa lihat laporannya sendiri
        if ($user instanceof Student && $report->student_id !== $user->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($report->load('student', 'counselor', 'chats'));
    }

    /**
     * Menerima laporan (hanya guru)
     */
    public function accept(Request $request, Report $report)
    {
        if (!$request->user() instanceof User) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $report->update([
            'counselor_id' => $request->user()->id,
            'status' => 'diterima',
        ]);

        return response()->json($report);
    }

    /**
     * Mengatur jadwal pertemuan (hanya guru)
     */
    public function schedule(Request $request, Report $report)
    {
        if (!$request->user() instanceof User || $report->type !== 'offline') {
            return response()->json(['message' => 'Akses ditolak atau laporan bukan tipe offline.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'scheduled_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report->update($validator->validated());

        return response()->json($report);
    }

    /**
     * Menyelesaikan laporan (hanya guru)
     */
    public function complete(Request $request, Report $report)
    {
        if (!$request->user() instanceof User) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $report->update(['status' => 'selesai']);

        return response()->json($report);
    }
}

