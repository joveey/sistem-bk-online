<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use App\Models\User;

class AuthController extends Controller
{
    public function studentLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unique_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student = Student::where('unique_code', $request->unique_code)->first();

        if (!$student) {
            return response()->json(['message' => 'Kode unik tidak valid.'], 401);
        }

        $token = $student->createToken('student-auth-token')->plainTextToken;

        return response()->json([
            'user' => $student,
            'token' => $token,
            'token_type' => 'Bearer',
            'user_type' => 'student'
        ]);
    }

    public function guruLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Kredensial tidak valid.'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('guru-auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'user_type' => 'counselor'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Berhasil logout.']);
    }
}
