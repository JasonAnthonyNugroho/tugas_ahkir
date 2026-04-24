<?php

namespace App\Http\Controllers;

use App\Models\Pertandingan;
use App\Models\Team;
use Illuminate\Http\Request;

class PertandinganController extends Controller
{
    public function index()
    {
        // Menampilkan pertandingan yang sedang LIVE atau Terjadwal
        $pertandingans = Pertandingan::with(['teamA', 'teamB'])
            ->whereIn('status', ['scheduled', 'live'])
            ->orderBy('waktu_tanding', 'asc')
            ->get();

        return view('dashboard', compact('pertandingans'));
    }

    public function adminDashboard()
    {
        $teams = Team::orderBy('name', 'asc')->get();
        $pertandingans = Pertandingan::with(['teamA', 'teamB'])
            ->orderBy('waktu_tanding', 'desc')
            ->get();

        return view('admin.dashboard', compact('teams', 'pertandingans'));
    }

    public function history()
    {
        $history = Pertandingan::where('status', 'finished')
            ->with(['teamA', 'teamB'])
            ->get()
            ->groupBy(function ($item) {
                return $item->waktu_tanding->format('Y');
            });

        return view('history', compact('history'));
    }

    public function store(Request $request)
    {
        Pertandingan::create([
            'team_a_id' => $request->team_a,
            'team_b_id' => $request->team_b,
            'waktu_tanding' => $request->waktu,
            'lokasi' => $request->lokasi,
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function updateScore(Request $request, Pertandingan $pertandingan)
    {
        $pertandingan->update([
            'score_a' => $request->score_a,
            'score_b' => $request->score_b,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Skor diperbarui!']);
    }
}
