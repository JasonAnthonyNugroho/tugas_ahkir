<?php

namespace Database\Seeders;

use App\Models\Pertandingan;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Archive2024Seeder extends Seeder
{
    private array $prodiMap = [
        'SI' => 'Sistem Informasi',
        'TEOL' => 'Teologi',
        'TI' => 'Informatika',
        'FKHUM' => 'Fakultas Hukum',
        'FK' => 'Kedokteran',
        'ARSI' => 'Arsitektur',
        'BIOTEK' => 'Biotek',
        'MENE' => 'Manajemen',
        'MANA' => 'Manajemen',
        'DESPRO' => 'Desain Produk',
        'AKUN' => 'Akuntansi',
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $mlSport = Sport::firstOrCreate(
                ['nama_sport' => 'Mobile Legends', 'sub_kategori' => 'BO3'],
                ['icon' => 'bi-controller', 'keterangan' => 'Arsip 2024 Mobile Legends']
            );

            $badmintonSport = Sport::firstOrCreate(
                ['nama_sport' => 'Badminton', 'sub_kategori' => 'Ganda Campuran'],
                ['icon' => 'bi-trophy', 'keterangan' => 'Arsip 2024 Badminton Ganda Campuran']
            );

            $this->seedMobileLegends2024($mlSport);
            $this->seedBadmintonGandaCampuran2024($badmintonSport);
            $this->syncExistingTeamProdiAliases();
        });
    }

    private function seedMobileLegends2024(Sport $sport): void
    {
        $tournament = Tournament::updateOrCreate(
            ['name' => 'Rector Cup 2024 - Mobile Legends', 'year' => 2024],
            ['sport_id' => $sport->id, 'type' => 'single_elimination']
        );

        $this->resetTournamentMatches($tournament);

        $teamNames = [
            'FKHUM B', 'TEOL A', 'BIOTEK B', 'MENE B',
            'DESPRO B', 'AKUN A', 'TI A', 'SI B',
            'DESPRO A', 'BIOTEK A', 'TI B', 'FKHUM A',
            'TEOL B', 'MENE A', 'FK A', 'SI A',
        ];

        $teams = collect($teamNames)->mapWithKeys(function ($name) {
            $team = Team::firstOrCreate(['name' => $name], ['prodi' => $this->resolveProdi($name)]);
            if (empty($team->prodi) || $team->prodi === $team->name) {
                $team->update(['prodi' => $this->resolveProdi($name)]);
            }
            return [$name => $team];
        });

        $tournament->teams()->sync($teams->pluck('id')->all());

        $matches = [];
        $now = now();
        $round1Winners = ['TEOL A', 'MENE B', 'DESPRO B', 'TI A', 'DESPRO A', 'TI B', 'MENE A', 'FK A'];

        for ($i = 0; $i < 8; $i++) {
            $teamA = $teams[$teamNames[$i * 2]];
            $teamB = $teams[$teamNames[$i * 2 + 1]];
            $winner = $teams[$round1Winners[$i]];
            $scoreA = $winner->id === $teamA->id ? 2 : 0;
            $scoreB = $winner->id === $teamB->id ? 2 : 0;

            $matches['r1'][] = Pertandingan::create([
                'sport_id' => $sport->id,
                'tournament_id' => $tournament->id,
                'round' => 1,
                'match_number' => $i + 1,
                'team_a_id' => $teamA->id,
                'team_b_id' => $teamB->id,
                'winner_id' => $winner->id,
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'babak' => 'Round of 16',
                'format_tanding' => 'bo3',
                'status' => 'finished',
                'lokasi' => 'GOR UKDW',
                'waktu_tanding' => $now->copy()->setDate(2024, 10, 4)->setTime(14 + $i, 0),
                'selesai_pada' => $now->copy()->setDate(2024, 10, 4)->setTime(14 + $i, 50),
            ]);
        }

        $quarterWinners = ['MENE B', 'TI A', 'TI B', 'FK A'];
        for ($i = 0; $i < 4; $i++) {
            $teamA = $matches['r1'][$i * 2]->winner;
            $teamB = $matches['r1'][$i * 2 + 1]->winner;
            $winner = $teams[$quarterWinners[$i]];
            $scoreA = $winner?->id === $teamA?->id ? 2 : 0;
            $scoreB = $winner?->id === $teamB?->id ? 2 : 0;

            $matches['r2'][] = Pertandingan::create([
                'sport_id' => $sport->id,
                'tournament_id' => $tournament->id,
                'round' => 2,
                'match_number' => $i + 1,
                'team_a_id' => $teamA?->id,
                'team_b_id' => $teamB?->id,
                'winner_id' => $winner?->id,
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'babak' => 'Quarterfinal',
                'format_tanding' => 'bo3',
                'status' => 'finished',
                'lokasi' => 'GOR UKDW',
                'waktu_tanding' => $now->copy()->setDate(2024, 10, 5)->setTime(17 + $i, 0),
                'selesai_pada' => $now->copy()->setDate(2024, 10, 5)->setTime(17 + $i, 50),
            ]);
        }

        $semiWinners = ['TI A', 'FK A'];
        for ($i = 0; $i < 2; $i++) {
            $teamA = $matches['r2'][$i * 2]->winner;
            $teamB = $matches['r2'][$i * 2 + 1]->winner;
            $winner = $teams[$semiWinners[$i]];
            $scoreA = $winner?->id === $teamA?->id ? 2 : 0;
            $scoreB = $winner?->id === $teamB?->id ? 2 : 0;

            $matches['r3'][] = Pertandingan::create([
                'sport_id' => $sport->id,
                'tournament_id' => $tournament->id,
                'round' => 3,
                'match_number' => $i + 1,
                'team_a_id' => $teamA?->id,
                'team_b_id' => $teamB?->id,
                'winner_id' => $winner?->id,
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'babak' => 'Semi Final',
                'format_tanding' => 'bo3',
                'status' => 'finished',
                'lokasi' => 'GOR UKDW',
                'waktu_tanding' => $now->copy()->setDate(2024, 10, 5)->setTime(19 + $i, 0),
                'selesai_pada' => $now->copy()->setDate(2024, 10, 5)->setTime(19 + $i, 50),
            ]);
        }

        $final = Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 4,
            'match_number' => 1,
            'team_a_id' => $teams['TI A']->id,
            'team_b_id' => $teams['FK A']->id,
            'winner_id' => $teams['TI A']->id,
            'score_a' => 2,
            'score_b' => 0,
            'babak' => 'Final',
            'format_tanding' => 'bo3',
            'status' => 'finished',
            'lokasi' => 'GOR UKDW',
            'waktu_tanding' => $now->copy()->setDate(2024, 10, 6)->setTime(19, 0),
            'selesai_pada' => $now->copy()->setDate(2024, 10, 6)->setTime(19, 50),
        ]);

        Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 4,
            'match_number' => 99,
            'team_a_id' => $teams['MENE B']->id,
            'team_b_id' => $teams['TI B']->id,
            'winner_id' => $teams['MENE B']->id,
            'score_a' => 2,
            'score_b' => 0,
            'babak' => 'Perebutan Juara 3',
            'format_tanding' => 'bo3',
            'status' => 'finished',
            'lokasi' => 'GOR UKDW',
            'waktu_tanding' => $now->copy()->setDate(2024, 10, 6)->setTime(16, 0),
            'selesai_pada' => $now->copy()->setDate(2024, 10, 6)->setTime(16, 50),
        ]);

        // next match relation
        foreach ($matches['r1'] as $index => $match) {
            $match->update(['next_match_id' => $matches['r2'][(int) floor($index / 2)]->id]);
        }
        foreach ($matches['r2'] as $index => $match) {
            $match->update(['next_match_id' => $matches['r3'][(int) floor($index / 2)]->id]);
        }
        $matches['r3'][0]->update(['next_match_id' => $final->id]);
        $matches['r3'][1]->update(['next_match_id' => $final->id]);
    }

    private function seedBadmintonGandaCampuran2024(Sport $sport): void
    {
        $tournament = Tournament::updateOrCreate(
            ['name' => 'Rector Cup 2024 - Badminton Ganda Campuran', 'year' => 2024],
            ['sport_id' => $sport->id, 'type' => 'single_elimination']
        );

        $this->resetTournamentMatches($tournament);

        $teamNames = [
            'TI A', 'TEOL B', 'SI A', 'FKHUM A', 'FK A', 'DESPRO A', 'TEOL A', 'ARSI A',
            'BIOTEK A', 'ARSI B', 'DESPRO B', 'ARSI C', 'MENE A', 'FKHUM B', 'SI A', 'MENE A',
        ];
        $teams = collect($teamNames)->unique()->mapWithKeys(function ($name) {
            $team = Team::firstOrCreate(['name' => $name], ['prodi' => $this->resolveProdi($name)]);
            if (empty($team->prodi) || $team->prodi === $team->name) {
                $team->update(['prodi' => $this->resolveProdi($name)]);
            }
            return [$name => $team];
        });
        $tournament->teams()->syncWithoutDetaching($teams->pluck('id')->all());

        $now = now();
        $round1 = [];
        $round1Pairs = [
            ['TI A', 'TEOL B', 'TEOL B'],
            ['SI A', 'FKHUM A', 'SI A'],
            ['FK A', 'DESPRO A', 'FK A'],
            ['TEOL A', 'ARSI A', 'TEOL A'],
            ['BIOTEK A', 'ARSI B', 'ARSI B'],
            ['DESPRO B', 'ARSI C', 'ARSI C'],
            ['MENE A', 'FKHUM B', 'MENE A'],
        ];

        foreach ($round1Pairs as $i => $pair) {
            [$teamAName, $teamBName, $winnerName] = $pair;
            $teamA = $teams[$teamAName];
            $teamB = $teams[$teamBName];
            $winner = $teams[$winnerName];
            $scoreA = $winner->id === $teamA->id ? 2 : 0;
            $scoreB = $winner->id === $teamB->id ? 2 : 0;

            $round1[] = Pertandingan::create([
                'sport_id' => $sport->id,
                'tournament_id' => $tournament->id,
                'round' => 1,
                'match_number' => $i + 1,
                'team_a_id' => $teamA->id,
                'team_b_id' => $teamB->id,
                'winner_id' => $winner->id,
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'babak' => 'Round of 16',
                'status' => 'finished',
                'lokasi' => 'GOR UKDW',
                'waktu_tanding' => $now->copy()->setDate(2024, 10, 18)->setTime(16, 30)->addMinutes($i * 40),
                'selesai_pada' => $now->copy()->setDate(2024, 10, 18)->setTime(17, 0)->addMinutes($i * 40),
            ]);
        }

        $quarterPairs = [
            ['TEOL B', 'SI A', 'SI A'],
            ['FK A', 'TEOL A', 'FK A'],
            ['ARSI B', 'ARSI C', 'ARSI C'],
            ['MENE A', null, 'MENE A'],
        ];
        $quarter = [];
        foreach ($quarterPairs as $i => $pair) {
            [$teamAName, $teamBName, $winnerName] = $pair;
            $teamA = $teams[$teamAName];
            $teamB = $teamBName ? $teams[$teamBName] : null;
            $winner = $teams[$winnerName];
            $scoreA = $winner->id === $teamA->id ? 2 : 0;
            $scoreB = ($teamB && $winner->id === $teamB->id) ? 2 : 0;

            $quarter[] = Pertandingan::create([
                'sport_id' => $sport->id,
                'tournament_id' => $tournament->id,
                'round' => 2,
                'match_number' => $i + 1,
                'team_a_id' => $teamA->id,
                'team_b_id' => $teamB?->id,
                'winner_id' => $winner->id,
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'babak' => 'Quarterfinal',
                'status' => 'finished',
                'lokasi' => 'GOR UKDW',
                'waktu_tanding' => $now->copy()->setDate(2024, 10, 19)->setTime(17, 10)->addMinutes($i * 40),
                'selesai_pada' => $now->copy()->setDate(2024, 10, 19)->setTime(17, 50)->addMinutes($i * 40),
            ]);
        }

        $semi = [];
        $semiPairs = [
            ['SI A', 'FK A', 'FK A'],
            ['ARSI C', 'MENE A', 'ARSI C'],
        ];
        foreach ($semiPairs as $i => $pair) {
            [$teamAName, $teamBName, $winnerName] = $pair;
            $teamA = $teams[$teamAName];
            $teamB = $teams[$teamBName];
            $winner = $teams[$winnerName];
            $scoreA = $winner->id === $teamA->id ? 2 : 0;
            $scoreB = $winner->id === $teamB->id ? 2 : 0;

            $semi[] = Pertandingan::create([
                'sport_id' => $sport->id,
                'tournament_id' => $tournament->id,
                'round' => 3,
                'match_number' => $i + 1,
                'team_a_id' => $teamA->id,
                'team_b_id' => $teamB->id,
                'winner_id' => $winner->id,
                'score_a' => $scoreA,
                'score_b' => $scoreB,
                'babak' => 'Semi Final',
                'status' => 'finished',
                'lokasi' => 'GOR UKDW',
                'waktu_tanding' => $now->copy()->setDate(2024, 10, 19)->setTime(17, 30)->addMinutes($i * 40),
                'selesai_pada' => $now->copy()->setDate(2024, 10, 19)->setTime(18, 0)->addMinutes($i * 40),
            ]);
        }

        $final = Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 4,
            'match_number' => 1,
            'team_a_id' => $teams['FK A']->id,
            'team_b_id' => $teams['ARSI C']->id,
            'winner_id' => $teams['FK A']->id,
            'score_a' => 2,
            'score_b' => 0,
            'babak' => 'Final',
            'status' => 'finished',
            'lokasi' => 'GOR UKDW',
            'waktu_tanding' => $now->copy()->setDate(2024, 10, 19)->setTime(18, 0),
            'selesai_pada' => $now->copy()->setDate(2024, 10, 19)->setTime(18, 40),
        ]);

        $third = Pertandingan::create([
            'sport_id' => $sport->id,
            'tournament_id' => $tournament->id,
            'round' => 4,
            'match_number' => 99,
            'team_a_id' => $teams['SI A']->id,
            'team_b_id' => $teams['MENE A']->id,
            'winner_id' => $teams['MENE A']->id,
            'score_a' => 0,
            'score_b' => 2,
            'babak' => 'Perebutan Juara 3',
            'status' => 'finished',
            'lokasi' => 'GOR UKDW',
            'waktu_tanding' => $now->copy()->setDate(2024, 10, 19)->setTime(17, 0),
            'selesai_pada' => $now->copy()->setDate(2024, 10, 19)->setTime(17, 40),
        ]);

        foreach ($round1 as $index => $match) {
            $match->update(['next_match_id' => $quarter[(int) floor($index / 2)]->id]);
        }
        foreach ($quarter as $index => $match) {
            $match->update(['next_match_id' => $semi[(int) floor($index / 2)]->id]);
        }
        $semi[0]->update(['next_match_id' => $final->id]);
        $semi[1]->update(['next_match_id' => $final->id]);
        $third->update(['next_match_id' => null]);
    }

    private function resetTournamentMatches(Tournament $tournament): void
    {
        $matchIds = $tournament->pertandingans()->pluck('id');
        if ($matchIds->isNotEmpty()) {
            DB::table('match_games')->whereIn('pertandingan_id', $matchIds)->delete();
            Pertandingan::whereIn('id', $matchIds)->delete();
        }
    }

    private function resolveProdi(string $teamName): string
    {
        foreach ($this->prodiMap as $alias => $prodi) {
            if (str_starts_with(strtoupper($teamName), $alias)) {
                return $prodi;
            }
        }

        return $teamName;
    }

    private function syncExistingTeamProdiAliases(): void
    {
        Team::query()->get()->each(function (Team $team) {
            $resolved = $this->resolveProdi($team->name);
            if ($resolved !== $team->name) {
                $team->update(['prodi' => $resolved]);
            }
        });
    }
}
