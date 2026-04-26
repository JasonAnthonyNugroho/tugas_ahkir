@extends('layouts.app')

@section('title', 'Kelola Skor')

@section('content')
    <div class="mb-5">
        <h2 class="font-weight-bold mb-1">Kelola Skor & Status</h2>
        <p class="text-muted">Update hasil pertandingan secara real-time untuk penonton.</p>
    </div>

    <div class="row">
        @php $liveMatches = $pertandingans->where('status', 'live'); @endphp

        <div class="col-12 mb-4">
            <div class="d-flex align-items-center">
                <div class="badge-live mr-3">
                    <span class="live-dot"></span> LIVE NOW
                </div>
                <div class="flex-grow-1 border-bottom border-secondary" style="opacity: 0.1;"></div>
            </div>
        </div>

        @if($liveMatches->isEmpty())
            <div class="col-12 mb-5">
                <div class="card border-0 py-5 text-center" style="background: rgba(255,255,255,0.02);">
                    <div class="card-body">
                        <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                            style="width: 80px; height: 80px; background: rgba(255,255,255,0.05) !important;">
                            <i class="bi bi-broadcast text-muted h2 mb-0"></i>
                        </div>
                        <h5 class="text-white font-weight-bold">Tidak Ada Pertandingan Live</h5>
                        <p class="text-muted small mx-auto" style="max-width: 400px;">Aktifkan pertandingan dari daftar jadwal
                            di bawah untuk mulai memperbarui skor secara real-time.</p>
                    </div>
                </div>
            </div>
        @else
            @foreach($liveMatches as $p)
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 border-0 shadow-lg" style="border-top: 4px solid #ef4444 !important;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="badge-primary">
                                    <i class="bi {{ $p->sport->icon ?? 'bi-trophy' }} mr-2"></i>
                                    {{ $p->sport->nama_sport ?? 'Tournament' }}
                                </span>
                                <span class="text-muted small"><i class="bi bi-geo-alt mr-1"></i> {{ $p->lokasi }}</span>
                            </div>

                            <form action="{{ url('/pertandingan/' . $p->id . '/update-score') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="bg-dark-subtle rounded-xl p-4 mb-4"
                                    style="background: rgba(15, 23, 42, 0.3); border-radius: 20px; border: 1px solid var(--glass-border);">
                                    @if(strtoupper($p->sport->nama_sport ?? '') == 'PUBG MOBILE')
                                        <div class="text-center">
                                            <label class="small font-weight-bold text-muted text-uppercase mb-3 d-block">Total Points
                                                (Battle Royale)</label>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <input type="number" name="score_a"
                                                    class="form-control form-control-lg text-center font-weight-bold text-white bg-transparent border-0"
                                                    style="font-size: 3rem; width: 150px;" value="{{ $p->score_a }}">
                                                <span class="h3 text-primary mb-0 ml-2">PTS</span>
                                            </div>
                                            <input type="hidden" name="score_b" value="0">
                                            <p class="text-muted small mt-2">{{ $p->teamA?->name ?? 'TBD' }}</p>
                                        </div>
                                    @else
                                        <div class="row align-items-center text-center">
                                            <div class="col-5">
                                                <label
                                                    class="small font-weight-bold text-muted text-uppercase mb-2 d-block text-truncate">{{ $p->teamA?->name ?? 'TBD' }}</label>
                                                <input type="number" name="score_a"
                                                    class="form-control form-control-lg text-center font-weight-bold text-white bg-transparent border-0"
                                                    style="font-size: 2.5rem;" value="{{ $p->score_a }}">
                                            </div>
                                            <div class="col-2 p-0">
                                                <div class="h3 text-muted mb-0">:</div>
                                            </div>
                                            <div class="col-5">
                                                <label
                                                    class="small font-weight-bold text-muted text-uppercase mb-2 d-block text-truncate">{{ $p->teamB?->name ?? 'TBD' }}</label>
                                                <input type="number" name="score_b"
                                                    class="form-control form-control-lg text-center font-weight-bold text-white bg-transparent border-0"
                                                    style="font-size: 2.5rem;" value="{{ $p->score_b }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Screenshot
                                            Bukti</label>
                                        <div class="custom-file modern-file-input">
                                            <input type="file" name="screenshot" class="custom-file-input" id="ss{{ $p->id }}">
                                            <label class="custom-file-label" for="ss{{ $p->id }}">
                                                {{ $p->screenshot ? 'Update Screenshot' : 'Upload Gambar' }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="small font-weight-bold text-muted text-uppercase mb-2">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="live" selected>TETAP LIVE</option>
                                            <option value="finished">SELESAI (ARCHIVE)</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block font-weight-bold mt-2 py-3 shadow-lg">
                                    <i class="bi bi-cloud-arrow-up-fill mr-2"></i> UPDATE SKOR SEKARANG
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <div class="col-12 mt-5 mb-4">
            <div class="d-flex align-items-center">
                <h5 class="text-white font-weight-bold mb-0 mr-3">AKTIVASI JADWAL</h5>
                <div class="flex-grow-1 border-bottom border-secondary" style="opacity: 0.1;"></div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Sport</th>
                                    <th>Waktu</th>
                                    <th>Pertandingan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pertandingans->where('status', 'scheduled') as $p)
                                    <tr>
                                        <td>
                                            <span class="badge px-3 py-1"
                                                style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); border-radius: 8px; font-weight: 600;">
                                                {{ $p->sport->nama_sport }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-white small font-weight-bold">
                                                {{ $p->waktu_tanding->format('H:i') }}</div>
                                            <div class="text-muted small">{{ $p->waktu_tanding->format('d M') }}</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-600 text-uppercase small">
                                                @if(strtoupper($p->sport->nama_sport) == 'PUBG MOBILE')
                                                    {{ $p->teamA->name }}
                                                @else
                                                    {{ $p->teamA->name }} <span class="text-muted mx-2">VS</span>
                                                    {{ $p->teamB->name }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success rounded-pill px-4 shadow-sm font-weight-bold"
                                                data-toggle="modal" data-target="#quickLive{{ $p->id }}">
                                                Mulai Live
                                            </button>

                                            {{-- Modal Quick Live --}}
                                            <div class="modal fade" id="quickLive{{ $p->id }}" tabindex="-1" role="dialog">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content"
                                                        style="background: var(--bg-dark); border: 1px solid var(--glass-border); border-radius: 24px;">
                                                        <div class="modal-header border-0 p-4"
                                                            style="background: linear-gradient(135deg, #10b981, #059669) !important; border-radius: 24px 24px 0 0;">
                                                            <h5 class="modal-title text-white font-weight-bold">Aktivasi
                                                                Pertandingan</h5>
                                                            <button type="button" class="close text-white"
                                                                data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <form action="{{ url('/pertandingan/' . $p->id . '/update-score') }}"
                                                            method="POST">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="score_a" value="0">
                                                            <input type="hidden" name="score_b" value="0">
                                                            <input type="hidden" name="status" value="live">
                                                            <div class="modal-body text-center p-5">
                                                                <div class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                                                    style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1);">
                                                                    <i class="bi bi-play-fill text-success h3 mb-0"></i>
                                                                </div>
                                                                <p class="text-muted mb-2">Mulai pertandingan live untuk:</p>
                                                                <h4 class="text-white font-weight-bold mb-4">
                                                                    @if(strtoupper($p->sport->nama_sport) == 'PUBG MOBILE')
                                                                        {{ $p->teamA->name }}
                                                                    @else
                                                                        {{ $p->teamA->name }} VS {{ $p->teamB->name }}
                                                                    @endif
                                                                </h4>
                                                                <p class="text-muted small mb-0">Pertandingan akan segera muncul
                                                                    di dashboard publik.</p>
                                                            </div>
                                                            <div class="modal-footer border-0 p-4">
                                                                <button type="button"
                                                                    class="btn btn-link text-muted font-weight-bold text-decoration-none"
                                                                    data-dismiss="modal">Batal</button>
                                                                <button type="submit"
                                                                    class="btn btn-success px-5 font-weight-bold">Mulai
                                                                    Sekarang</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">Tidak ada jadwal pertandingan
                                            mendatang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modern-file-input .custom-file-label {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            color: var(--text-muted);
            border-radius: 12px;
        }

        .modern-file-input .custom-file-label::after {
            background: var(--accent-primary);
            color: white;
            border-radius: 0 12px 12px 0;
            padding: 0.375rem 1rem;
        }
    </style>
@endsection