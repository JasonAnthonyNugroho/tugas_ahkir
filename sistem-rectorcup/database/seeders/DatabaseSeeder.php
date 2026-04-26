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
            'email' => 'admin@ukdw.ac.id',
            'password' => bcrypt('admin#1234'),
        ]);

        // Data Cabang Olahraga
        $sports = [
            ['nama_sport' => 'PES', 'icon' => 'bi-controller'],
            ['nama_sport' => 'PUBG MOBILE', 'icon' => 'bi-phone'],
            ['nama_sport' => 'Mobile Legends', 'icon' => 'bi-controller'],
            ['nama_sport' => 'Basket Putra', 'icon' => 'bi-basketball'],
            ['nama_sport' => 'Basket Putri', 'icon' => 'bi-basketball'],
            ['nama_sport' => 'Badminton', 'icon' => 'bi-trophy'],
            ['nama_sport' => 'Billiard', 'icon' => 'bi-circle-fill'],
            ['nama_sport' => 'Volleyball', 'icon' => 'bi-dribbble'],
            ['nama_sport' => 'Futsal', 'icon' => 'bi-football'],
            ['nama_sport' => 'Vocal Group', 'icon' => 'bi-mic-fill'],
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

        // --- TAMBAHAN DATA SAMPLE PERTANDINGAN (20+) ---
        $sportList = Sport::all();
        $teamList = Team::where('name', '!=', 'Seluruh Prodi')->get();
        $battleRoyaleTeam = Team::where('name', 'Seluruh Prodi')->first();

        // 1. Tambahkan data History (Tahun Lalu - 2025)
        for ($i = 0; $i < 15; $i++) {
            $sport = $sportList[$i % $sportList->count()]; // Pastikan semua cabang olahraga muncul bergantian
            if (strtoupper($sport->nama_sport) == 'PUBG MOBILE') {
                \App\Models\Pertandingan::create([
                    'sport_id' => $sport->id,
                    'team_a_id' => $battleRoyaleTeam->id,
                    'team_b_id' => $battleRoyaleTeam->id,
                    'score_a' => rand(100, 500),
                    'score_b' => 0,
                    'waktu_tanding' => '2025-05-' . rand(10, 25) . ' 10:00:00',
                    'lokasi' => 'Online / GOR UKDW',
                    'status' => 'finished'
                ]);
            } else {
                $teams = $teamList->random(2);
                \App\Models\Pertandingan::create([
                    'sport_id' => $sport->id,
                    'team_a_id' => $teams[0]->id,
                    'team_b_id' => $teams[1]->id,
                    'score_a' => rand(0, 5),
                    'score_b' => rand(0, 5),
                    'waktu_tanding' => '2025-05-' . rand(10, 25) . ' 14:00:00',
                    'lokasi' => 'GOR UKDW',
                    'status' => 'finished'
                ]);
            }
        }

        // 2. Tambahkan data Live / Scheduled / Finished (Tahun Ini - 2026)
        $locations = ['GOR UKDW', 'Lapangan Basket', 'Atrium Didaktos', 'Lab Komputer'];
        for ($i = 0; $i < 12; $i++) {
            $sport = $sportList->random();
            if ($i < 3) {
                $status = 'live';
            } elseif ($i < 6) {
                $status = 'finished';
            } else {
                $status = 'scheduled';
            }

            if (strtoupper($sport->nama_sport) == 'PUBG MOBILE') {
                \App\Models\Pertandingan::create([
                    'sport_id' => $sport->id,
                    'team_a_id' => $battleRoyaleTeam->id,
                    'team_b_id' => $battleRoyaleTeam->id,
                    'score_a' => in_array($status, ['live', 'finished']) ? rand(50, 500) : 0,
                    'score_b' => 0,
                    'waktu_tanding' => '2026-04-26 ' . (10 + $i) . ':00:00',
                    'lokasi' => 'Online',
                    'status' => $status
                ]);
            } else {
                $teams = $teamList->random(2);
                \App\Models\Pertandingan::create([
                    'sport_id' => $sport->id,
                    'team_a_id' => $teams[0]->id,
                    'team_b_id' => $teams[1]->id,
                    'score_a' => in_array($status, ['live', 'finished']) ? rand(0, 5) : 0,
                    'score_b' => in_array($status, ['live', 'finished']) ? rand(0, 5) : 0,
                    'waktu_tanding' => '2026-04-26 ' . (13 + $i) . ':30:00',
                    'lokasi' => $locations[array_rand($locations)],
                    'status' => $status
                ]);
            }
        }

        // 3. Tambahkan Sample Tournament Bracket (Futsal)
        $futsal = Sport::where('nama_sport', 'Futsal')->first();
        if ($futsal) {
            $tournament = \App\Models\Tournament::create([
                'name' => 'Rector Cup Futsal 2026',
                'sport_id' => $futsal->id,
                'type' => 'single_elimination',
                'year' => 2026,
            ]);

            $bracketTeams = Team::where('name', '!=', 'Seluruh Prodi')->limit(4)->get();
            $tournament->teams()->attach($bracketTeams->pluck('id'));

            // Round 2 (Final)
            $final = \App\Models\Pertandingan::create([
                'sport_id' => $futsal->id,
                'tournament_id' => $tournament->id,
                'round' => 2,
                'match_number' => 1,
                'status' => 'scheduled',
                'babak' => 'Final',
                'waktu_tanding' => '2026-05-01 19:00:00',
                'lokasi' => 'GOR UKDW',
            ]);

            // Round 1 (Semi Finals)
            foreach ([1, 2] as $num) {
                \App\Models\Pertandingan::create([
                    'sport_id' => $futsal->id,
                    'tournament_id' => $tournament->id,
                    'round' => 1,
                    'match_number' => $num,
                    'team_a_id' => $bracketTeams[($num - 1) * 2]->id,
                    'team_b_id' => $bracketTeams[($num - 1) * 2 + 1]->id,
                    'next_match_id' => $final->id,
                    'status' => 'scheduled',
                    'babak' => 'Semi Final',
                    'waktu_tanding' => '2026-04-30 ' . (15 + $num) . ':00:00',
                    'lokasi' => 'GOR UKDW',
                ]);
            }
        }
    }
}
