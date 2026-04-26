<?php

namespace App\Http\Controllers;

use App\Events\ScoreUpdated;
use App\Models\Pertandingan;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PertandinganController extends Controller
{
    public function index()
    {
        // Auto-update status scheduled ke live jika waktu sudah terlewati
        Pertandingan::autoUpdateLiveStatus();

        // Menampilkan pertandingan yang sedang LIVE atau Terjadwal
        $pertandingans = Pertandingan::with(['teamA', 'teamB', 'sport'])
            ->whereIn('status', ['scheduled', 'live'])
            ->whereNull('tournament_id') // Tampilkan yang mandiri saja di list utama
            ->orderBy('waktu_tanding', 'asc')
            ->get();

        // Ambil tournament yang aktif
        $tournaments = Tournament::with(['sport', 'pertandingans.teamA', 'pertandingans.teamB', 'pertandingans.winner'])
            ->where('is_active', true)
            ->get();

        return view('dashboard', compact('pertandingans', 'tournaments'));
    }

    public function adminDashboard()
    {
        // Auto-update status scheduled ke live jika waktu sudah terlewati
        Pertandingan::autoUpdateLiveStatus();

        $teams = Team::orderBy('name', 'asc')->get();
        $sports = Sport::orderBy('nama_sport', 'asc')->get();
        $pertandingans = Pertandingan::with(['teamA', 'teamB', 'sport'])
            ->orderBy('waktu_tanding', 'desc')
            ->get();

        return view('admin.dashboard', compact('teams', 'sports', 'pertandingans'));
    }

    public function history()
    {
        $selectedYear = request('year', 'all');

        $query = Pertandingan::where('status', 'finished')
            ->with(['teamA', 'teamB', 'sport', 'games'])
            ->orderBy('waktu_tanding', 'desc');

        if ($selectedYear !== 'all') {
            $query->whereYear('waktu_tanding', $selectedYear);
        }

        $history = $query->get()
            ->groupBy(function ($item) {
                return $item->waktu_tanding->format('Y');
            });

        // Ambil tournament yang sudah selesai
        $tournaments = Tournament::with(['sport', 'pertandingans.teamA', 'pertandingans.teamB', 'pertandingans.winner', 'pertandingans.games'])
            ->whereHas('pertandingans', function ($q) {
                $q->where('status', 'finished');
            })
            ->when($selectedYear !== 'all', function ($q) use ($selectedYear) {
                $q->where('year', $selectedYear);
            })
            ->get();

        $years = Pertandingan::where('status', 'finished')
            ->selectRaw('YEAR(waktu_tanding) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('history', compact('history', 'years', 'selectedYear', 'tournaments'));
    }

    public function store(Request $request)
    {
        $pertandingan = Pertandingan::create([
            'sport_id' => $request->sport_id,
            'team_a_id' => $request->team_a,
            'team_b_id' => $request->team_b,
            'waktu_tanding' => $request->waktu,
            'lokasi' => $request->lokasi,
            'status' => 'scheduled',
        ]);

        // Broadcast event pertandingan baru
        broadcast(new \App\Events\MatchCreated($pertandingan));

        return redirect()->route('admin.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function manageScore()
    {
        // Auto-update status scheduled ke live jika waktu sudah terlewati
        Pertandingan::autoUpdateLiveStatus();

        $pertandingans = Pertandingan::with(['teamA', 'teamB', 'sport'])
            ->orderBy('status', 'asc') // live akan muncul lebih dulu
            ->orderBy('waktu_tanding', 'desc')
            ->get();

        return view('admin.skor', compact('pertandingans'));
    }

    public function updateScore(Request $request, Pertandingan $pertandingan)
    {
        $request->validate([
            'score_a' => 'required|integer',
            'score_b' => 'required|integer',
            'status' => 'required|string',
            'screenshot' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'game_scores' => 'nullable|array',
            'game_screenshots.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'score_a' => $request->score_a,
            'score_b' => $request->score_b,
            'status' => $request->status,
        ];

        // Handle Screenshot Utama
        if ($request->hasFile('screenshot')) {
            if ($pertandingan->screenshot && file_exists(public_path('storage/' . $pertandingan->screenshot))) {
                unlink(public_path('storage/' . $pertandingan->screenshot));
            }
            $path = $request->file('screenshot')->store('screenshots', 'public');
            $updateData['screenshot'] = $path;
        }

        // Handle BO3 Games Data
        if ($request->has('game_scores')) {
            foreach ($request->game_scores as $gameNum => $scores) {
                $game = $pertandingan->games()->updateOrCreate(
                    ['game_number' => $gameNum],
                    [
                        'score_a' => $scores['a'] ?? 0,
                        'score_b' => $scores['b'] ?? 0,
                        'winner_id' => ($scores['a'] ?? 0) > ($scores['b'] ?? 0) ? $pertandingan->team_a_id : (($scores['b'] ?? 0) > ($scores['a'] ?? 0) ? $pertandingan->team_b_id : null),
                    ]
                );

                // Handle Game Screenshot
                if ($request->hasFile("game_screenshots.$gameNum")) {
                    if ($game->screenshot && file_exists(public_path('storage/' . $game->screenshot))) {
                        unlink(public_path('storage/' . $game->screenshot));
                    }
                    $path = $request->file("game_screenshots.$gameNum")->store('screenshots/games', 'public');
                    $game->update(['screenshot' => $path]);
                }
            }
        }

        // Logika Pengarsipan Otomatis & Auto-Advance Bracket
        if ($request->status == 'finished' && $pertandingan->status != 'finished') {
            $updateData['selesai_pada'] = now();

            if ($request->score_a > $request->score_b) {
                $updateData['winner_id'] = $pertandingan->team_a_id;
            } elseif ($request->score_b > $request->score_a) {
                $updateData['winner_id'] = $pertandingan->team_b_id;
            }

            $pertandingan->update($updateData);

            if ($pertandingan->tournament_id && $pertandingan->next_match_id && isset($updateData['winner_id'])) {
                $nextMatch = Pertandingan::find($pertandingan->next_match_id);
                if ($nextMatch) {
                    if ($pertandingan->match_number % 2 != 0) {
                        $nextMatch->update(['team_a_id' => $updateData['winner_id']]);
                    } else {
                        $nextMatch->update(['team_b_id' => $updateData['winner_id']]);
                    }
                }
            }
        } else {
            $pertandingan->update($updateData);
        }

        broadcast(new ScoreUpdated($pertandingan));

        return back()->with('success', 'Data pertandingan berhasil diperbarui!');
    }

    public function show(Pertandingan $pertandingan)
    {
        return view('detail', compact('pertandingan'));
    }

    public function generateBracket(Request $request)
    {
        $request->validate([
            'tournament_name' => 'required|string',
            'sport_id' => 'required|exists:sports,id',
            'team_ids' => 'required|array|min:2',
        ]);

        return DB::transaction(function () use ($request) {
            $tournament = Tournament::create([
                'name' => $request->tournament_name,
                'sport_id' => $request->sport_id,
                'type' => 'single_elimination',
                'year' => date('Y'),
            ]);

            $teamIds = $request->team_ids;
            shuffle($teamIds);
            $tournament->teams()->attach($teamIds);

            $numTeams = count($teamIds);
            $numRounds = ceil(log($numTeams, 2));
            $totalMatchesNeeded = pow(2, $numRounds) - 1;

            $matches = [];
            $matchIndex = 1;

            // Buat semua placeholder match dulu dari babak akhir ke awal agar bisa link next_match_id
            // Tapi lebih mudah buat per babak dan simpan ref-nya.

            $roundMatches = [];

            // 1. Buat struktur match kosong untuk setiap babak
            for ($r = $numRounds; $r >= 1; $r--) {
                $numMatchesInRound = pow(2, $numRounds - $r);
                $roundMatches[$r] = [];

                for ($m = 1; $m <= $numMatchesInRound; $m++) {
                    $nextMatch = null;
                    if ($r < $numRounds) {
                        $nextMatchIndex = ceil($m / 2) - 1;
                        $nextMatch = $roundMatches[$r + 1][$nextMatchIndex];
                    }

                    $babakName = $this->getBabakName($r, $numRounds);

                    $match = Pertandingan::create([
                        'sport_id' => $request->sport_id,
                        'tournament_id' => $tournament->id,
                        'round' => $r,
                        'match_number' => $m,
                        'next_match_id' => $nextMatch ? $nextMatch->id : null,
                        'status' => 'scheduled',
                        'babak' => $babakName,
                        'waktu_tanding' => now()->addDays($r), // Placeholder waktu
                        'lokasi' => 'TBA',
                    ]);

                    $roundMatches[$r][] = $match;
                }
            }

            // 2. Isi Round 1 dengan tim yang ada
            $round1Matches = array_reverse($roundMatches[1]); // Karena kita buat r=1 terakhir di loop
            // Tunggu, loop saya r=numRounds down to 1. Jadi r=1 adalah yang terakhir dibuat.
            // Mari perbaiki urutan loop agar r=1 dibuat pertama atau simpan dengan benar.

            // Re-logic:
            // Round 1 (paling banyak match) -> Round Final (1 match)
            // Tapi untuk link next_match_id, kita butuh match di Round r+1 sudah ada.
            // Jadi buat Final dulu (Round numRounds), lalu Semi-Final, dst.

            // Isi Round 1 (yang dibuat terakhir di loop r=numRounds down to 1)
            $r1Matches = $roundMatches[1];
            for ($i = 0; $i < $numTeams; $i += 2) {
                $matchIdx = $i / 2;
                if (isset($r1Matches[$matchIdx])) {
                    $update = ['team_a_id' => $teamIds[$i]];
                    if (isset($teamIds[$i + 1])) {
                        $update['team_b_id'] = $teamIds[$i + 1];
                    }
                    $r1Matches[$matchIdx]->update($update);
                }
            }

            return redirect()->route('admin.index')->with('success', 'Bracket Tournament berhasil digenerate!');
        });
    }

    private function getBabakName($round, $totalRounds)
    {
        $diff = $totalRounds - $round;
        if ($diff == 0)
            return 'Final';
        if ($diff == 1)
            return 'Semi Final';
        if ($diff == 2)
            return 'Quarter Final';
        return 'Babak ' . $round;
    }
}
