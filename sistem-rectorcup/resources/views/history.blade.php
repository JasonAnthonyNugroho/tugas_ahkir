@extends('layouts.app')
@section('title', 'Riwayat Pertandingan')

@section('content')
    <style>
        .modern-filter-group {
            border-radius: 20px !important;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            background: rgba(15, 23, 42, 0.3);
        }

        .modern-filter-group .input-group-text {
            border-radius: 20px 0 0 20px !important;
            padding-left: 1.25rem;
            padding-right: 0.5rem;
            border: none;
            position: relative;
        }

        .modern-filter-group .form-control {
            border-radius: 0 20px 20px 0 !important;
            border: none;
            padding-left: 0.5rem;
            color: white !important;
        }

        .modern-filter-group .form-control option {
            background-color: var(--bg-dark);
            color: white;
        }

        /* Hapus Animasi Cursor Mengetik */
        .typing-cursor {
            position: relative;
        }

        .filter-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
        }
    </style>

    <div class="mb-5">
        <h2 class="font-weight-bold mb-1">Arsip Rector Cup</h2>
        <p class="text-muted">Jelajahi riwayat pertandingan dari tahun ke tahun.</p>
    </div>

    <div class="mb-5">
        <div class="card filter-card p-4">
            <form action="{{ route('history') }}" method="GET" class="row align-items-center">
                <div class="col-md-auto mb-3 mb-md-0">
                    <label class="text-muted small text-uppercase font-weight-bold mb-0 mr-3">Filter Tahun:</label>
                </div>
                <div class="col-md-4">
                    <div class="input-group modern-filter-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent">
                                <i class="bi bi-calendar-event text-primary typing-cursor"></i>
                            </span>
                        </div>
                        <select name="year" class="form-control" style="background: transparent;"
                            onchange="this.form.submit()">
                            <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>
                                Semua Tahun
                            </option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                    Edisi Tahun {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($history->isEmpty())
        <div class="card border-0 py-5 text-center" style="background: rgba(255,255,255,0.02);">
            <div class="card-body">
                <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                    style="width: 80px; height: 80px; background: rgba(255,255,255,0.05) !important;">
                    <i class="bi bi-archive text-muted h2 mb-0"></i>
                </div>
                <h3 class="font-weight-bold">Belum Ada Riwayat</h3>
                <p class="text-muted mx-auto" style="max-width: 400px;">Data pertandingan untuk periode ini belum tersedia.</p>
            </div>
        </div>
    @else
        @foreach($history as $tahun => $daftarPertandingan)
            <div class="year-section mb-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                        style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)) !important; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);">
                        <span class="text-white font-weight-bold">{{ substr($tahun, 2) }}</span>
                    </div>
                    <h3 class="font-weight-bold mb-0 text-white">
                        Edisi Rector Cup {{ $tahun }}
                    </h3>
                    <div class="flex-grow-1 ml-4 border-bottom border-secondary" style="opacity: 0.1;"></div>
                </div>

                <div class="row">
                    @foreach($daftarPertandingan as $p)
                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <span class="badge-primary">
                                            <i class="bi {{ $p->sport->icon ?? 'bi-trophy' }} mr-2"></i>
                                            {{ $p->sport->nama_sport ?? 'Tournament' }}
                                        </span>
                                        <span class="text-muted small">
                                            <i class="bi bi-check-circle-fill text-success mr-1"></i> Selesai
                                        </span>
                                    </div>

                                    @if(strtoupper($p->sport->nama_sport ?? '') == 'PUBG MOBILE')
                                        <div class="text-center py-3">
                                            <h5 class="text-muted small text-uppercase font-weight-bold mb-2">Total Points</h5>
                                            <div class="display-3 font-weight-bold text-white mb-2"
                                                style="text-shadow: 0 0 20px rgba(99, 102, 241, 0.3);">{{ $p->score_a }}</div>
                                            <h4 class="font-weight-bold text-uppercase tracking-wide">{{ $p->teamA?->name ?? 'TBD' }}</h4>
                                        </div>
                                    @else
                                        <div class="row text-center align-items-center py-3">
                                            <div class="col-5">
                                                <h4 class="h6 font-weight-bold text-truncate mb-3">{{ $p->teamA?->name ?? 'TBD' }}</h4>
                                                <div class="display-4 font-weight-bold text-white">{{ $p->score_a }}</div>
                                            </div>
                                            <div class="col-2 p-0">
                                                <div class="text-muted font-weight-bold small">VS</div>
                                            </div>
                                            <div class="col-5">
                                                <h4 class="h6 font-weight-bold text-truncate mb-3">{{ $p->teamB?->name ?? 'TBD' }}</h4>
                                                <div class="display-4 font-weight-bold text-white">{{ $p->score_b }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    <div
                                        class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between align-items-center">
                                        <div class="small text-muted">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-geo-alt mr-2"></i> {{ $p->lokasi }}
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-check mr-2"></i>
                                                {{ \Carbon\Carbon::parse($p->waktu_tanding)->format('d M Y') }}
                                            </div>
                                        </div>
                                        <a href="{{ route('pertandingan.show', $p->id) }}"
                                            class="btn btn-primary btn-sm rounded-pill px-3">
                                            Review <i class="bi bi-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
@endsection