<?php

namespace Database\Seeders;

use App\Models\Pertandingan;
use App\Models\Sport;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HistorySampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = Sport::all();
        $teams = Team::where('name', '!=', 'Seluruh Prodi')->get();
        $pubgSport = Sport::where('nama_sport', 'PUBG MOBILE')->first();
        $seluruhProdi = Team::where('name', 'Seluruh Prodi')->first();

        if ($sports->isEmpty() || $teams->count() < 2) {
            return;
        }

        for ($i = 1; $i <= 13; $i++) {
            $isPubg = ($i % 5 == 0) && $pubgSport; // Sesekali buat PUBG
            
            $waktu = Carbon::create(2025, rand(1, 12), rand(1, 28), rand(8, 20), 0, 0);
            
            if ($isPubg) {
                Pertandingan::create([
                    'sport_id' => $pubgSport->id,
                    'team_a_id' => $seluruhProdi->id,
                    'team_b_id' => $seluruhProdi->id,
                    'score_a' => rand(100, 500),
                    'score_b' => 0,
                    'waktu_tanding' => $waktu,
                    'lokasi' => 'Online / Discord Rector Cup',
                    'status' => 'finished',
                    'selesai_pada' => $waktu->addHours(2),
                    'babak' => 'Final Day',
                ]);
            } else {
                $teamA = $teams->random();
                $teamB = $teams->where('id', '!=', $teamA->id)->random();
                $sport = $sports->where('id', '!=', $pubgSport->id)->random();

                Pertandingan::create([
                    'sport_id' => $sport->id,
                    'team_a_id' => $teamA->id,
                    'team_b_id' => $teamB->id,
                    'score_a' => rand(0, 10),
                    'score_b' => rand(0, 10),
                    'waktu_tanding' => $waktu,
                    'lokasi' => 'GOR UKDW',
                    'status' => 'finished',
                    'selesai_pada' => $waktu->addHours(1),
                    'babak' => 'Group Stage',
                ]);
            }
        }
    }
}
