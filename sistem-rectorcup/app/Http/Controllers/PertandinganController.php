<?php

namespace App\Http\Controllers;

use App\Events\MatchCreated;
use App\Events\MatchStatusUpdated;
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
        $selectedSport = request('sport_id');

        // Tampilkan semua match yang sedang live atau terjadwal (termasuk dari tournament).
        // FILTER: Hanya tampilkan pertandingan yang BUKAN TBD (kedua tim sudah terisi)
        $query = Pertandingan::with(['teamA', 'teamB', 'sport', 'games'])
            ->whereIn('status', ['live', 'scheduled'])
            ->whereNotNull('team_a_id')
            ->whereNotNull('team_b_id');

        if ($selectedSport && $selectedSport !== 'all') {
            $query->where('sport_id', $selectedSport);
        }

        $pertandingans = $query->orderBy('waktu_tanding', 'asc')
            ->get()
            ->sortBy(function ($item) {
                return $item->status === 'live' ? 0 : 1;
            })->values();

        $sports = Sport::all();
        
        // Get active tournaments with their data
        $tournaments = Tournament::with(['sport', 'teams', 'pertandingans' => function($q) {
                $q->whereIn('status', ['live', 'finished'])
                  ->orderBy('round', 'desc')
                  ->limit(5);
            }])
            ->where('year', date('Y'))
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('dashboard', compact('pertandingans', 'sports', 'selectedSport', 'tournaments'));
    }

    public function adminDashboard()
    {
        $teams = Team::orderBy('name', 'asc')->get();
        $sports = Sport::orderBy('nama_sport', 'asc')->get();

        // Grouping pertandingans
        $pertandingans = Pertandingan::with(['teamA', 'teamB', 'sport', 'tournament'])
            ->orderBy('waktu_tanding', 'desc')
            ->get();

        $groupedMatches = $this->groupMatchesByTournament($pertandingans);

        $tournaments = Tournament::with(['sport', 'teams'])->where('year', date('Y'))->get();

        return view('admin.dashboard', compact('teams', 'sports', 'pertandingans', 'groupedMatches', 'tournaments'));
    }

    public function quickUpdate(Request $request, Pertandingan $pertandingan)
    {
        $request->validate([
            'waktu_tanding' => 'nullable|date',
            'lokasi' => 'nullable|string',
            'team_a_id' => 'nullable|exists:teams,id',
            'team_b_id' => 'nullable|exists:teams,id',
        ]);

        $pertandingan->update($request->only(['waktu_tanding', 'lokasi', 'team_a_id', 'team_b_id']));

        return back()->with('success', 'Detail pertandingan berhasil diperbarui!');
    }

    public function bulkLive(Request $request)
    {
        $request->validate([
            'match_ids' => 'required|array',
            'match_ids.*' => 'exists:pertandingans,id',
        ]);

        $matches = Pertandingan::whereIn('id', $request->match_ids)
            ->where('status', 'scheduled')
            ->get();
        
        Pertandingan::whereIn('id', $request->match_ids)
            ->where('status', 'scheduled')
            ->update(['status' => 'live']);
        
        // Broadcast status update untuk setiap match
        foreach ($matches as $match) {
            broadcast(new MatchStatusUpdated($match->id, 'live', [
                'id' => $match->id,
                'status' => 'live',
                'team_a' => $match->teamA?->name,
                'team_b' => $match->teamB?->name,
                'sport' => $match->sport?->nama_sport,
            ]));
        }

        return back()->with('success', count($request->match_ids) . ' pertandingan berhasil diaktifkan ke LIVE!');
    }



    public function history()
    {
        $selectedYear = request('year', 'all');
        $selectedSportId = request('sport_id', 'all');
        $selectedTournamentId = request('tournament_id');

        // Ambil data tournament yang dipilih jika ada
        $selectedTournament = null;
        if ($selectedTournamentId) {
            $selectedTournament = Tournament::with(['sport', 'pertandingans.teamA', 'pertandingans.teamB', 'pertandingans.winner', 'pertandingans.games'])
                ->find($selectedTournamentId);
        }

        $query = Pertandingan::where('status', 'finished')
            ->with(['teamA', 'teamB', 'sport', 'games'])
            ->orderBy('waktu_tanding', 'desc');

        if ($selectedYear !== 'all') {
            $query->whereYear('waktu_tanding', $selectedYear);
        }

        if ($selectedSportId !== 'all') {
            $query->where('sport_id', $selectedSportId);
        }

        $history = $query->get()
            ->groupBy(function ($item) {
                return $item->waktu_tanding->format('Y');
            });

        // Ambil tournament yang sudah selesai
        $tournamentsQuery = Tournament::with(['sport', 'pertandingans.teamA', 'pertandingans.teamB', 'pertandingans.winner', 'pertandingans.games'])
            ->withCount('teams')
            ->whereHas('pertandingans', function ($q) {
                $q->where('status', 'finished');
            });

        if ($selectedYear !== 'all') {
            $tournamentsQuery->where('year', $selectedYear);
        }

        if ($selectedSportId !== 'all') {
            $tournamentsQuery->where('sport_id', $selectedSportId);
        }

        $tournaments = $tournamentsQuery->orderBy('year', 'desc')->get();

        $pertandinganYears = Pertandingan::where('status', 'finished')
            ->select('waktu_tanding')
            ->get()
            ->map(function ($item) {
                return $item->waktu_tanding->format('Y');
            })
            ->unique();

        $tournamentYears = Tournament::select('year')
            ->pluck('year')
            ->unique();

        $years = $pertandinganYears->merge($tournamentYears)
            ->unique()
            ->sortDesc()
            ->values();

        $sports = Sport::all();

        return view('history', compact('history', 'years', 'selectedYear', 'tournaments', 'selectedTournament', 'sports', 'selectedSportId'));
    }

    public function store(Request $request)
    {
        $pertandingan = Pertandingan::create([
            'sport_id' => $request->sport_id,
            'team_a_id' => $request->team_a,
            'team_b_id' => $request->team_b,
            'waktu_tanding' => $request->waktu,
            'lokasi' => $request->lokasi,
            'keterangan' => $request->keterangan,
            'format_tanding' => in_array($request->format_tanding, ['BO1', 'BO3']) ? $request->format_tanding : 'BO1',
            'status' => 'scheduled',
        ]);

        // Broadcast event pertandingan baru
        broadcast(new MatchCreated($pertandingan));

        return redirect()->route('admin.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function manageScore()
    {
        $pertandingans = Pertandingan::with(['teamA', 'teamB', 'sport', 'games', 'tournament'])
            ->orderBy('status', 'asc') // live akan muncul lebih dulu
            ->orderBy('waktu_tanding', 'asc') // yang paling awal/jadul dulu
            ->get();

        $groupedMatches = $this->groupMatchesByTournament($pertandingans);

        $tournaments = Tournament::with(['sport'])->whereHas('pertandingans', function ($q) {
            $q->whereIn('status', ['live', 'scheduled']);
        })->get();

        return view('admin.skor', compact('pertandingans', 'groupedMatches', 'tournaments'));
    }

    public function updateScore(Request $request, Pertandingan $pertandingan)
    {
        // Cegah update skor jika salah satu tim masih TBD
        if (!$pertandingan->team_a_id || !$pertandingan->team_b_id) {
            return back()->with('error', 'Tidak bisa update skor — salah satu tim masih TBD. Selesaikan pertandingan sebelumnya terlebih dahulu.');
        }

        $request->validate([
            'score_a'              => 'required|integer',
            'score_b'              => 'required|integer',
            'status'               => 'required|string',
            'keterangan'           => 'nullable|string|max:255',
            'screenshot'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'game_scores'          => 'nullable|array',
            'game_screenshots'     => 'nullable|array',
            'game_screenshots.*'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $updateData = [
            'score_a' => $request->score_a,
            'score_b' => $request->score_b,
            'status'  => $request->status,
        ];

        // Add keterangan if provided
        if ($request->has('keterangan')) {
            $updateData['keterangan'] = $request->keterangan;
        }

        // Persiapkan detail folder bertingkat: [Tahun] / [Nama Sport] / [Tanggal]
        $sportName = $pertandingan->sport->nama_sport ?? 'Sport';
        $year = $pertandingan->waktu_tanding ? $pertandingan->waktu_tanding->format('Y') : now()->format('Y');
        $date = $pertandingan->waktu_tanding ? $pertandingan->waktu_tanding->format('d-m-Y') : now()->format('d-m-Y');

        $cleanSportName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $sportName);
        $cleanYear = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $year);
        $cleanDate = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $date);

        $localFolder = "{$cleanYear}/{$cleanSportName}/{$cleanDate}";

        $this->handleMainScreenshot($request, $pertandingan, $localFolder, $cleanSportName, $cleanDate, $updateData);
        $this->handleGameScores($request, $pertandingan);
        $this->handleGameScreenshots($request, $pertandingan, $localFolder, $cleanSportName, $cleanDate);

        // Logika Pengarsipan Otomatis & Auto-Advance Bracket
        if ($request->status == 'finished' && $pertandingan->status != 'finished') {
            $this->handleMatchFinished($request, $pertandingan, $updateData);
        } else {
            $pertandingan->update($updateData);
        }

        // Refresh model to get latest data
        $pertandingan->refresh();

        $this->broadcastScoreUpdate($request, $pertandingan);

        return back()->with([
            'success' => 'Data pertandingan berhasil diperbarui!',
            'updated_id' => $pertandingan->id
        ]);
    }

    private function handleMainScreenshot(Request $request, Pertandingan $pertandingan, string $localFolder, string $cleanSportName, string $cleanDate, array &$updateData): void
    {
        if ($request->hasFile('screenshot')) {
            $prefix = "{$cleanSportName} - {$cleanDate}";
            $path = $this->storeScreenshot($request->file('screenshot'), $localFolder, $prefix, $pertandingan->screenshot);
            $updateData['screenshot'] = $path;
        }
    }

    private function handleGameScores(Request $request, Pertandingan $pertandingan): void
    {
        if ($request->has('game_scores')) {
            foreach ($request->game_scores as $gameNum => $scores) {
                $pertandingan->games()->updateOrCreate(
                    ['game_number' => $gameNum],
                    [
                        'score_a'   => $scores['a'] ?? 0,
                        'score_b'   => $scores['b'] ?? 0,
                        'winner_id' => ($scores['a'] ?? 0) > ($scores['b'] ?? 0)
                            ? $pertandingan->team_a_id
                            : (($scores['b'] ?? 0) > ($scores['a'] ?? 0) ? $pertandingan->team_b_id : null),
                    ]
                );
            }
        }
    }

    private function handleGameScreenshots(Request $request, Pertandingan $pertandingan, string $localFolder, string $cleanSportName, string $cleanDate): void
    {
        if ($pertandingan->format_tanding === 'BO3') {
            for ($gameNum = 1; $gameNum <= 3; $gameNum++) {
                if ($request->hasFile("game_screenshots.$gameNum")) {
                    $game = $pertandingan->games()->firstOrCreate(
                        ['game_number' => $gameNum],
                        ['score_a' => 0, 'score_b' => 0, 'winner_id' => null]
                    );

                    $prefix = "{$cleanSportName} - {$cleanDate}_game_{$gameNum}";
                    $path = $this->storeScreenshot($request->file("game_screenshots.$gameNum"), $localFolder, $prefix, $game->screenshot);
                    $game->update(['screenshot' => $path]);
                }
            }
        }
    }

    private function handleMatchFinished(Request $request, Pertandingan $pertandingan, array &$updateData): void
    {
        $updateData['selesai_pada'] = now();

        if ($request->score_a > $request->score_b) {
            $updateData['winner_id'] = $pertandingan->team_a_id;
        } elseif ($request->score_b > $request->score_a) {
            $updateData['winner_id'] = $pertandingan->team_b_id;
        }

        $pertandingan->update($updateData);

        if ($pertandingan->tournament_id && $pertandingan->next_match_id && isset($updateData['winner_id'])) {
            $nextMatch = Pertandingan::find($pertandingan->next_match_id);
            $loserId = ($updateData['winner_id'] == $pertandingan->team_a_id) ? $pertandingan->team_b_id : $pertandingan->team_a_id;

            if ($nextMatch) {
                if ($pertandingan->match_number % 2 != 0) {
                    $nextMatch->update(['team_a_id' => $updateData['winner_id']]);
                } else {
                    $nextMatch->update(['team_b_id' => $updateData['winner_id']]);
                }
            }

            if ($pertandingan->babak == 'Semi Final') {
                $thirdPlaceMatch = Pertandingan::where('tournament_id', $pertandingan->tournament_id)
                    ->where('babak', 'Perebutan Juara 3')
                    ->first();

                if ($thirdPlaceMatch) {
                    if ($pertandingan->match_number % 2 != 0) {
                        $thirdPlaceMatch->update(['team_a_id' => $loserId]);
                    } else {
                        $thirdPlaceMatch->update(['team_b_id' => $loserId]);
                    }
                }
            }
        }
    }

    private function broadcastScoreUpdate(Request $request, Pertandingan $pertandingan): void
    {
        broadcast(new ScoreUpdated($pertandingan));

        if ($request->status === 'finished') {
            broadcast(new MatchStatusUpdated($pertandingan->id, 'finished', [
                'id'     => $pertandingan->id,
                'status' => 'finished',
                'team_a' => $pertandingan->teamA?->name,
                'team_b' => $pertandingan->teamB?->name,
                'score_a'=> $pertandingan->score_a,
                'score_b'=> $pertandingan->score_b,
                'sport'  => $pertandingan->sport?->nama_sport,
            ]));
        }
    }

    private function storeScreenshot($file, string $folder, string $prefix, ?string $oldPath = null): string
    {
        if ($oldPath && file_exists(public_path('storage/' . $oldPath))) {
            @unlink(public_path('storage/' . $oldPath));
        }
        
        $timestamp = time();
        $extension = $file->getClientOriginalExtension();
        $fileName = "{$prefix}_{$timestamp}.{$extension}";
        
        return $file->storeAs($folder, $fileName, 'public');
    }

    private function groupMatchesByTournament($pertandingans)
    {
        return $pertandingans->groupBy(function ($item) {
            return $item->tournament_id ? 'tournament_' . $item->tournament_id : 'independent';
        });
    }

    public function apiLiveMatches()
    {
        $matches = Pertandingan::with(['teamA', 'teamB', 'sport'])
            ->whereIn('status', ['live', 'scheduled'])
            ->whereNotNull('team_a_id')
            ->whereNotNull('team_b_id')
            ->orderBy('waktu_tanding', 'asc')
            ->get()
            ->map(function ($p) {
                return [
                    'id'          => $p->id,
                    'status'      => $p->status,
                    'score_a'     => $p->score_a,
                    'score_b'     => $p->score_b,
                    'team_a'      => $p->teamA?->name ?? 'TBD',
                    'team_b'      => $p->teamB?->name ?? 'TBD',
                    'team_a_id'   => $p->team_a_id,
                    'team_b_id'   => $p->team_b_id,
                    'sport'       => $p->sport?->nama_sport,
                    'sport_icon'  => $p->sport?->icon ?? 'bi-trophy',
                    'lokasi'      => $p->lokasi,
                    'waktu'       => $p->waktu_tanding->format('d M, H:i'),
                    'detail_url'  => route('pertandingan.show', $p->id),
                ];
            });

        return response()->json([
            'matches'   => $matches,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function update(Request $request, Pertandingan $pertandingan)
    {
        $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'team_a_id' => 'nullable|exists:teams,id',
            'team_b_id' => 'nullable|exists:teams,id|different:team_a_id',
            'waktu_tanding' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $pertandingan->update([
            'sport_id' => $request->sport_id,
            'team_a_id' => $request->team_a_id, // Bisa null untuk TBD
            'team_b_id' => $request->team_b_id, // Bisa null untuk TBD
            'waktu_tanding' => $request->waktu_tanding,
            'lokasi' => $request->lokasi,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.index')->with('success', 'Pertandingan berhasil diperbarui!');
    }

    public function show(Pertandingan $pertandingan)
    {
        $pertandingan->load(['teamA', 'teamB', 'sport', 'games.winner']);
        return view('detail', compact('pertandingan'));
    }
}
