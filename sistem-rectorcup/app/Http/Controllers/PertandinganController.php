<?php

namespace App\Http\Controllers;

use App\Models\Pertandingan;
use App\Models\Team;
use Illuminate\Http\Request;

class PertandinganController extends Controller
{
    // // Tampilan Dashboard untuk Umum (Guest)
    public function index()
    {
        // Mengambil semua pertandingan beserta data timnya
        $pertandingans = Pertandingan::with(['teamA', 'teamB'])->get();

        return view('dashboard', compact('pertandingans'));
    }

    // Tampilan Dashboard untuk Panitia (Setelah Login)
    public function adminDashboard()
    {
        $pertandingans = Pertandingan::all();
        $teams = Team::orderBy('name', 'asc')->get(); // List tim urut abjad untuk input

        return view('admin.dashboard', compact('pertandingans', 'teams'));
    }

    // Fungsi menyimpan jadwal baru
    public function store(Request $request)
    {
        Pertandingan::create([
            'team_a_id' => $request->team_a,
            'team_b_id' => $request->team_b,
            'waktu_tanding' => $request->waktu,
            'lokasi' => $request->lokasi,
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }

    // Fungsi Update Skor Live
    public function updateScore(Request $request, Pertandingan $pertandingan)
    {
        $pertandingan->update([
            'score_a' => $request->score_a,
            'score_b' => $request->score_b,
            'status' => $request->status, // Bisa diubah ke 'live' atau 'finished'
        ]);

        return response()->json(['message' => 'Skor diperbarui!']);
    }
    public function history()
    {
        $history = Pertandingan::where('status', 'finished')
            ->with(['teamA', 'teamB'])
            ->get()
            ->groupBy(function ($date) {
                return \Carbon\Carbon::parse($date->waktu_tanding)->format('Y');
            });

        return view('history', compact('history'));
    }
}
