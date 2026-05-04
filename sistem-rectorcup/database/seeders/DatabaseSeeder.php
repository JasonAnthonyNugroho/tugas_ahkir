<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat Akun Panitia Rector Cup
        User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@ukdw.ac.id',
            'password' => bcrypt('admin#1234'),
        ]);

        // Data Cabang Olahraga
        $sports = [
            ['nama_sport' => 'PES', 'icon' => 'bi-controller'],
            ['nama_sport' => 'PUBG MOBILE', 'icon' => 'bi-phone'],
            ['nama_sport' => 'Mobile Legends', 'icon' => 'bi-controller'],
            ['nama_sport' => 'Basket', 'icon' => 'bi-basketball'],
            ['nama_sport' => 'Badminton', 'icon' => 'bi-trophy'],
            ['nama_sport' => 'Billiard', 'icon' => 'bi-circle-fill'],
            ['nama_sport' => 'Volleyball', 'icon' => 'bi-dribbble'],
            ['nama_sport' => 'Futsal', 'icon' => 'bi-football'],
            ['nama_sport' => 'Vocal Group', 'icon' => 'bi-mic-fill'],
            ['nama_sport' => 'Catur', 'icon' => 'bi-chess'],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }

        // Data Program Studi Sarjana UKDW (Dukungan Multi-Tim: A & B)
        $prodis = [
            'Informatika',
            'Sistem Informasi',
            'Arsitektur',
            'Desain Produk',
            'Manajemen',
            'Akuntansi',
            'Biologi',
            'Kedokteran',
            'Teologi',
            'Pendidikan Bahasa Inggris'
        ];

        // Tambahkan Tim Khusus Battle Royale
        Team::create([
            'name' => 'Seluruh Prodi',
            'prodi' => 'Semua Prodi'
        ]);

        foreach ($prodis as $prodi) {
            // Membuat Tim A
            Team::create([
                'name' => $prodi . ' A',
                'prodi' => $prodi
            ]);

            // Membuat Tim B
            Team::create([
                'name' => $prodi . ' B',
                'prodi' => $prodi
            ]);
        }
    }
}
