<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat Akun Panitia Rector Cup
        \App\Models\User::factory()->create([
            'name' => 'Panitia Rector Cup',
            'email' => 'admin@ukdw.ac.id',
            'password' => bcrypt('admin#1234'), // Password sesuai request Kamu
        ]);

        // Contoh Data Tim (Prodi)
        \App\Models\Team::create(['name' => 'Sistem Informasi A', 'prodi' => 'Sistem Informasi']);
        \App\Models\Team::create(['name' => 'Manajemen B', 'prodi' => 'Manajemen']);
    }
}
