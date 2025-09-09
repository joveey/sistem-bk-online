<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use App\Models\User; // Import model User

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan firstOrCreate untuk mencegah duplikasi data jika seeder dijalankan ulang
        // Parameter pertama: kondisi pencarian (email)
        // Parameter kedua: data yang akan dibuat jika tidak ditemukan
        User::firstOrCreate(
            ['email' => 'jovisywl@gmail.com'],
            [
                'name' => 'Joveey',
                'password' => Hash::make('jovi1234'), // Ganti 'password' dengan password yang aman
            ]
        );

        // Tambahkan guru lain jika perlu
        // User::firstOrCreate(
        //     ['email' => 'kepala.sekolah@sekolah.id'],
        //     [
        //         'name' => 'Kepala Sekolah Contoh',
        //         'password' => Hash::make('password123'),
        //     ]
        // );
    }
}