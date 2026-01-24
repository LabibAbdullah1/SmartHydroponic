<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun ADMIN (Bisa Edit)
        User::create([
        'name' => 'Admin SmartHidroponik',
        'email' => 'admin', // Ganti email Anda
        'password' => Hash::make('admin'), // Ganti password Anda
        'role' => 'admin',
        ]);
    }
}
