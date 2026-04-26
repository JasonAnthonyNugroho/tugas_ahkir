<?php

namespace Database\Seeders;

use App\Models\Pertandingan;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\MatchGame;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TournamentSampleSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Bersihkan data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pertandingan::truncate();
        Tournament::truncate();
        MatchGame::truncate();
        DB::table('tournament_teams')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Persiapan Cabang Olahraga
        $mlbb = Sport::where('nama_sport', 'Mobile Legends')->first();
        $badminton = Sport::where('nama_sport', 'Badminton')->first();

        if (!$mlbb || !$badminton) {
            $this->command->error('Sport Mobile Legends atau Badminton tidak ditemukan.');
            return;
        }

        // Ambil 12 tim dari prodi yang berbeda (menggunakan Tim A dari setiap prodi + 2 Tim B)
        $allTeams = Team::where('name', '!=', 'Seluruh Prodi')->get();
        $tournamentTeams = $allTeams->take(12);

        if ($tournamentTeams->count() < 12) {
            $this->command->error('Tim tidak cukup untuk membuat 12 prodi sample.');
            return;
        }

        // --- 1. TOURNAMENT MLBB 2025 ---
        $this->createTournament2025($mlbb, $tournamentTeams, 'Rector Cup MLBB 2025', true);

        // --- 2. TOURNAMENT BADMINTON 2025 ---
        $this->createTournament2025($badminton, $tournamentTeams, 'Rector Cup Badminton 2025', false);

        // --- 3. SAMPLE PERTANDINGAN MANDIRI (2025 & 2026) ---
        $this->createIndependentMatches($mlbb, $badminton, $allTeams);

        $this->command->info('Sample data 2025 & 2026 created successfully!');
    }

    private function createTournament2025($sport, $teams, $name, $isMLBB)
    {
        $tournament = Tournament::create([
            'name' => $name,
            'sport_id' => $sport->id,
            'type' => 'single_elimination',
            'year' => 2025,
        ]);

        $tournament->teams()->attach($teams->pluck('id'));

        // Logic Bracket untuk 12 Tim (Round 1: 8 tim bertanding, 4 tim bye ke QF)
        // Round 1 (16 Besar - tapi cuma 4 match)
        // Match 1-4 di Round 1, pemenangnya masuk ke QF
        // QF (8 tim: 4 pemenang R1 + 4 tim bye)

        // Final
        $final = Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 4,
            'match_number' => 1,
            'status' => 'finished',
            'babak' => 'Final',
            'waktu_tanding' => '2025-05-20 19:00:00',
            'lokasi' => 'GOR UKDW',
            'team_a_id' => $teams[0]->id,
            'team_b_id' => $teams[1]->id,
            'score_a' => $isMLBB ? 2 : 21,
            'score_b' => $isMLBB ? 1 : 15,
            'winner_id' => $teams[0]->id,
            'selesai_pada' => '2025-05-20 20:30:00'
        ]);

        // Semi Finals
        $semi1 = Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 3,
            'match_number' => 1,
            'next_match_id' => $final->id,
            'status' => 'finished',
            'babak' => 'Semi Final',
            'waktu_tanding' => '2025-05-18 14:00:00',
            'lokasi' => 'GOR UKDW',
            'team_a_id' => $teams[0]->id,
            'team_b_id' => $teams[2]->id,
            'score_a' => $isMLBB ? 2 : 21,
            'score_b' => $isMLBB ? 0 : 12,
            'winner_id' => $teams[0]->id,
        ]);

        $semi2 = Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 3,
            'match_number' => 2,
            'next_match_id' => $final->id,
            'status' => 'finished',
            'babak' => 'Semi Final',
            'waktu_tanding' => '2025-05-18 16:00:00',
            'lokasi' => 'GOR UKDW',
            'team_a_id' => $teams[1]->id,
            'team_b_id' => $teams[3]->id,
            'score_a' => $isMLBB ? 2 : 21,
            'score_b' => $isMLBB ? 1 : 19,
            'winner_id' => $teams[1]->id,
        ]);

        // Perebutan Juara 3
        Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 3,
            'match_number' => 99,
            'status' => 'finished',
            'babak' => 'Perebutan Juara 3',
            'waktu_tanding' => '2025-05-20 14:00:00',
            'lokasi' => 'GOR UKDW',
            'team_a_id' => $teams[2]->id,
            'team_b_id' => $teams[3]->id,
            'score_a' => $isMLBB ? 2 : 21,
            'score_b' => $isMLBB ? 0 : 10,
            'winner_id' => $teams[2]->id,
        ]);

        // Quarter Finals (Contoh QF 1 & 2 saja yang diisi tim lengkap)
        for ($i = 1; $i <= 4; $i++) {
            Pertandingan::create([
                'sport_id' => $sport->id,
                'tournament_id' => $tournament->id,
                'round' => 2,
                'match_number' => $i,
                'next_match_id' => ($i <= 2) ? $semi1->id : $semi2->id,
                'status' => 'finished',
                'babak' => 'Quarter Final',
                'waktu_tanding' => '2025-05-15 10:00:00',
                'lokasi' => 'GOR UKDW',
                'team_a_id' => $teams[$i + 3]->id,
                'team_b_id' => $teams[$i + 7]->id,
                'score_a' => $isMLBB ? 2 : 21,
                'score_b' => $isMLBB ? 0 : 5,
                'winner_id' => $teams[$i + 3]->id,
            ]);
        }
    }

    private function createIndependentMatches($mlbb, $badminton, $teams)
    {
        // Pertandingan Mandiri 2025 (Finished)
        Pertandingan::create([
            'sport_id' => $mlbb->id,
            'team_a_id' => $teams[0]->id,
            'team_b_id' => $teams[1]->id,
            'score_a' => 2,
            'score_b' => 1,
            'waktu_tanding' => '2025-06-10 14:00:00',
            'lokasi' => 'Lab Komputer',
            'status' => 'finished',
            'winner_id' => $teams[0]->id
        ]);

        // Pertandingan Mandiri 2026 (Live)
        Pertandingan::create([
            'sport_id' => $badminton->id,
            'team_a_id' => $teams[2]->id,
            'team_b_id' => $teams[3]->id,
            'score_a' => 18,
            'score_b' => 20,
            'waktu_tanding' => now()->format('Y-m-d H:i:s'),
            'lokasi' => 'GOR UKDW',
            'status' => 'live'
        ]);

        // Pertandingan Mandiri 2026 (Scheduled)
        Pertandingan::create([
            'sport_id' => $mlbb->id,
            'team_a_id' => $teams[4]->id,
            'team_b_id' => $teams[5]->id,
            'score_a' => 0,
            'score_b' => 0,
            'waktu_tanding' => now()->addDays(1)->format('Y-m-d 15:00:00'),
            'lokasi' => 'Online',
            'status' => 'scheduled'
        ]);
    }
}
