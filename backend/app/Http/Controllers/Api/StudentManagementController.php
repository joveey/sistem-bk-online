<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentManagementController extends Controller
{
    /**
     * Menampilkan daftar semua siswa.
     */
    public function index()
    {
        $students = Student::orderBy('name', 'asc')->get();
        return response()->json(['data' => $students]);
    }

    /**
     * Menyimpan siswa baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'unique_code' => 'required|string|max:255|unique:students,unique_code',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Buat dan simpan siswa baru
        $student = Student::create($validator->validated());

        return response()->json([
            'message' => 'Siswa berhasil ditambahkan.',
            'data' => $student
        ], 201);
    }

    /**
     * Menampilkan detail satu siswa.
     */
    public function show($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        }
        return response()->json(['data' => $student]);
    }

    /**
     * Mengupdate data siswa yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'unique_code' => 'required|string|max:255|unique:students,unique_code',
                Rule::unique('students')->ignore($student->id),
            ],
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student->update($validator->validated());

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => $student
        ]);
    }

    /**
     * Menghapus data siswa dari database.
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Siswa tidak ditemukan.'], 404);
        }

        $student->delete();

        return response()->json(['message' => 'Siswa berhasil dihapus.'], 200);
    }
}
