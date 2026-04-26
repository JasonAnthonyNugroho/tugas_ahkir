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

        .filter-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
        }

        .tournament-card:hover {
            transform: translateY(-10px);
            background: rgba(255,255,255,0.06) !important;
            border-color: var(--accent-primary) !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        
        .tournament-card {
            transition: all 0.3s ease;
        }
    </style>

    <div class="mb-5">
        <h2 class="font-weight-bold mb-1">Arsip Rector Cup</h2>
        <p class="text-muted">Jelajahi riwayat pertandingan dan bracket turnamen dari tahun ke tahun.</p>
    </div>

    {{-- Filter Tahun & Sport --}}
    <div class="mb-5">
        <div class="card filter-card p-4">
            <form action="{{ route('history') }}" method="GET" class="row align-items-center">
                <div class="col-md-5 mb-3 mb-md-0">
                    <div class="input-group modern-filter-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent">
                                <i class="bi bi-calendar-event text-primary"></i>
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
                <div class="col-md-5 mb-3 mb-md-0">
                    <div class="input-group modern-filter-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent">
                                <i class="bi bi-funnel text-primary"></i>
                            </span>
                        </div>
                        <select name="sport_id" class="form-control" style="background: transparent;"
                            onchange="this.form.submit()">
                            <option value="all" {{ $selectedSportId == 'all' ? 'selected' : '' }}>
                                Semua Cabang Olahraga
                            </option>
                            @foreach($sports as $sport)
                                <option value="{{ $sport->id }}" {{ $selectedSportId == $sport->id ? 'selected' : '' }}>
                                    {{ $sport->nama_sport }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('history') }}" class="btn btn-outline-secondary btn-block rounded-pill">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if($selectedTournament)
        {{-- Detail View untuk Tournament yang Dipilih (Bracket) --}}
        <div class="mb-5">
            <div class="mb-4">
                <a href="{{ route('history', ['year' => $selectedYear]) }}" class="btn text-muted p-0 d-flex align-items-center hover-white">
                    <i class="bi bi-arrow-left-circle h4 mb-0 mr-2"></i>
                    <span class="font-weight-bold">Kembali ke Daftar Turnamen</span>
                </a>
            </div>

            <div class="card p-4 mb-5" style="background: rgba(30, 41, 59, 0.4); border-radius: 24px; border: 1px solid var(--glass-border);">
                <div class="d-flex align-items-center mb-5">
                    <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center mr-3"
                        style="width: 50px; height: 50px; background: linear-gradient(135deg, #6366f1, #a855f7) !important;">
                        <i class="bi {{ $selectedTournament->sport->icon ?? 'bi-diagram-3' }} text-white h4 mb-0"></i>
                    </div>
                    <div>
                        <h2 class="font-weight-bold mb-0 text-white">{{ $selectedTournament->name }}</h2>
                        <p class="text-muted mb-0 small text-uppercase tracking-wider">{{ $selectedTournament->sport->nama_sport }} • {{ $selectedTournament->year }}</p>
                    </div>
                </div>

                {{-- Podium Winners --}}
                @php
                    $final = $selectedTournament->pertandingans->where('babak', 'Final')->first();
                    $thirdPlace = $selectedTournament->pertandingans->where('babak', 'Perebutan Juara 3')->first();
                    
                    $juara1 = $final && $final->winner_id ? $final->winner : null;
                    $juara2 = $final && $final->winner_id ? ($final->winner_id == $final->team_a_id ? $final->teamB : $final->teamA) : null;
                    $juara3 = $thirdPlace && $thirdPlace->winner_id ? $thirdPlace->winner : null;
                @endphp

                @if($juara1 || $juara2 || $juara3)
                    <div class="row justify-content-center mb-5">
                        <div class="col-12 mb-4">
                            <h5 class="text-center text-muted small font-weight-bold text-uppercase tracking-widest mb-4">Podium Pemenang</h5>
                            <div class="d-flex justify-content-center align-items-end">
                                {{-- Juara 2 --}}
                                @if($juara2)
                                    <div class="text-center mx-3 mb-2">
                                        <div class="team-avatar mb-3 mx-auto d-flex align-items-center justify-content-center" 
                                            style="width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 50%; border: 2px solid #94a3b8;">
                                            <i class="bi bi-shield-shaded h4 mb-0 text-muted"></i>
                                        </div>
                                        <div class="p-3 rounded-top bg-secondary" style="min-width: 120px; height: 80px; opacity: 0.8;">
                                            <div class="h5 font-weight-bold text-white mb-0">2nd</div>
                                            <div class="small text-white-50 text-truncate" style="max-width: 100px;">{{ $juara2->name }}</div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Juara 1 --}}
                                @if($juara1)
                                    <div class="text-center mx-3">
                                        <i class="bi bi-crown-fill text-warning h3 mb-2 d-block"></i>
                                        <div class="team-avatar mb-3 mx-auto d-flex align-items-center justify-content-center" 
                                            style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%; border: 3px solid #fbbf24; box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);">
                                            <i class="bi bi-shield-fill h3 mb-0 text-warning"></i>
                                        </div>
                                        <div class="p-3 rounded-top bg-warning" style="min-width: 140px; height: 110px;">
                                            <div class="h4 font-weight-bold text-dark mb-0">1st</div>
                                            <div class="small font-weight-bold text-dark text-truncate" style="max-width: 120px;">{{ $juara1->name }}</div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Juara 3 --}}
                                @if($juara3)
                                    <div class="text-center mx-3 mb-2">
                                        <div class="team-avatar mb-3 mx-auto d-flex align-items-center justify-content-center" 
                                            style="width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 50%; border: 2px solid #b45309;">
                                            <i class="bi bi-shield-shaded h4 mb-0 text-muted"></i>
                                        </div>
                                        <div class="p-3 rounded-top" style="min-width: 120px; height: 60px; background: #b45309; opacity: 0.8;">
                                            <div class="h5 font-weight-bold text-white mb-0">3rd</div>
                                            <div class="small text-white-50 text-truncate" style="max-width: 100px;">{{ $juara3->name }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Bracket View --}}
                <div class="bracket-wrapper overflow-auto pb-4">
                    <div class="d-flex" style="min-width: max-content;">
                        @php
                            $maxRound = $selectedTournament->pertandingans->max('round');
                        @endphp

                        @for($r = 1; $r <= $maxRound; $r++)
                            @php
                                $roundMatches = $selectedTournament->pertandingans->where('round', $r)->where('match_number', '!=', 99)->sortBy('match_number');
                            @endphp
                            <div class="bracket-round mr-5" style="width: 280px;">
                                <h5 class="text-center text-muted small font-weight-bold text-uppercase mb-4">
                                    {{ $roundMatches->first()->babak ?? 'Babak ' . $r }}
                                </h5>
                                <div class="d-flex flex-column justify-content-around h-100">
                                    @foreach($roundMatches as $match)
                                        <a href="{{ route('pertandingan.show', $match->id) }}" class="bracket-match-link text-decoration-none">
                                            <div class="bracket-match mb-4 p-3 rounded"
                                                style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); position: relative; transition: all 0.3s ease;">
                                                {{-- Tim A --}}
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="small font-weight-bold {{ $match->winner_id == $match->team_a_id && $match->team_a_id ? 'text-primary' : 'text-white' }}">
                                                        {{ $match->teamA?->name ?? 'TBD' }}
                                                    </span>
                                                    <span class="badge {{ $match->winner_id == $match->team_a_id && $match->team_a_id ? 'badge-primary' : 'badge-dark' }} px-2">
                                                        {{ $match->score_a }}
                                                    </span>
                                                </div>
                                                {{-- Tim B --}}
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small font-weight-bold {{ $match->winner_id == $match->team_b_id && $match->team_b_id ? 'text-primary' : 'text-white' }}">
                                                        {{ $match->teamB?->name ?? 'TBD' }}
                                                    </span>
                                                    <span class="badge {{ $match->winner_id == $match->team_b_id && $match->team_b_id ? 'badge-primary' : 'badge-dark' }} px-2">
                                                        {{ $match->score_b }}
                                                    </span>
                                                </div>
                                                <div class="mt-2 text-center">
                                                    <span class="text-muted" style="font-size: 0.6rem;"><i class="bi bi-search mr-1"></i> Klik Detail</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endfor

                        {{-- Kolom Khusus Perebutan Juara 3 --}}
                        @php
                            $thirdPlaceMatch = $selectedTournament->pertandingans->where('match_number', 99)->first();
                        @endphp
                        @if($thirdPlaceMatch)
                            <div class="bracket-round mr-5" style="width: 280px;">
                                <h5 class="text-center text-muted small font-weight-bold text-uppercase mb-4">
                                    {{ $thirdPlaceMatch->babak }}
                                </h5>
                                <div class="d-flex flex-column justify-content-center h-100">
                                    <a href="{{ route('pertandingan.show', $thirdPlaceMatch->id) }}" class="bracket-match-link text-decoration-none">
                                        <div class="bracket-match mb-4 p-3 rounded"
                                            style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); position: relative; transition: all 0.3s ease;">
                                            {{-- Tim A --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="small font-weight-bold {{ $thirdPlaceMatch->winner_id == $thirdPlaceMatch->team_a_id && $thirdPlaceMatch->team_a_id ? 'text-primary' : 'text-white' }}">
                                                    {{ $thirdPlaceMatch->teamA?->name ?? 'TBD' }}
                                                </span>
                                                <span class="badge {{ $thirdPlaceMatch->winner_id == $thirdPlaceMatch->team_a_id && $thirdPlaceMatch->team_a_id ? 'badge-primary' : 'badge-dark' }} px-2">
                                                    {{ $thirdPlaceMatch->score_a }}
                                                </span>
                                            </div>
                                            {{-- Tim B --}}
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="small font-weight-bold {{ $thirdPlaceMatch->winner_id == $thirdPlaceMatch->team_b_id && $thirdPlaceMatch->team_b_id ? 'text-primary' : 'text-white' }}">
                                                    {{ $thirdPlaceMatch->teamB?->name ?? 'TBD' }}
                                                </span>
                                                <span class="badge {{ $thirdPlaceMatch->winner_id == $thirdPlaceMatch->team_b_id && $thirdPlaceMatch->team_b_id ? 'badge-primary' : 'badge-dark' }} px-2">
                                                    {{ $thirdPlaceMatch->score_b }}
                                                </span>
                                            </div>
                                            <div class="mt-2 text-center">
                                                <span class="text-muted" style="font-size: 0.6rem;"><i class="bi bi-search mr-1"></i> Klik Detail</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Daftar Tournament --}}
        @if($tournaments->isNotEmpty())
            <div class="mb-5">
                <h4 class="font-weight-bold text-white mb-4"><i class="bi bi-trophy text-warning mr-2"></i> Hasil Tournament</h4>
                <div class="row">
                    @foreach($tournaments as $tournament)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <a href="{{ route('history', ['tournament_id' => $tournament->id, 'year' => $selectedYear]) }}" class="text-decoration-none">
                                <div class="card h-100 tournament-card border-0" 
                                    style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border) !important; border-radius: 24px;">
                                    <div class="card-body p-4 text-center">
                                        <div class="sport-icon-container mb-3 mx-auto d-flex align-items-center justify-content-center"
                                            style="width: 70px; height: 70px; background: linear-gradient(135deg, #6366f1, #a855f7); border-radius: 20px;">
                                            <i class="bi {{ $tournament->sport->icon ?? 'bi-controller' }} text-white h2 mb-0"></i>
                                        </div>
                                        
                                        <h5 class="font-weight-bold text-white mb-1">{{ strtoupper($tournament->sport->nama_sport) }}</h5>
                                        <p class="text-muted small text-uppercase tracking-widest mb-3">{{ $tournament->name }}</p>
                                        
                                        <div class="d-flex justify-content-center align-items-center mb-3">
                                            <div class="px-3 py-1 rounded-pill mr-2" style="background: rgba(99, 102, 241, 0.1);">
                                                <span class="text-primary font-weight-bold small">
                                                    {{ $tournament->teams_count }} TIM
                                                </span>
                                            </div>
                                            <div class="px-3 py-1 rounded-pill" style="background: rgba(255, 255, 255, 0.05);">
                                                <span class="text-white font-weight-bold small">
                                                    {{ $tournament->year }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                            Lihat Bracket
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Daftar Pertandingan Mandiri --}}
        @if($history->isNotEmpty())
            <h4 class="font-weight-bold text-white mb-4"><i class="bi bi-calendar-check text-primary mr-2"></i> Daftar Pertandingan</h4>
            @foreach($history as $tahun => $daftarPertandingan)
                <div class="year-section mb-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                            style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)) !important;">
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
                                <div class="card h-100 shadow-sm border-0" style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 20px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <span class="badge badge-primary px-3 py-1" style="border-radius: 100px;">
                                                <i class="bi {{ $p->sport->icon ?? 'bi-trophy' }} mr-2"></i>
                                                {{ $p->sport->nama_sport ?? 'Tournament' }}
                                            </span>
                                            <span class="text-muted small">
                                                <i class="bi bi-check-circle-fill text-success mr-1"></i> Selesai
                                            </span>
                                        </div>

                                        <div class="row text-center align-items-center py-3">
                                            <div class="col-5">
                                                <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">{{ $p->teamA?->name ?? 'TBD' }}</h4>
                                                <div class="h3 font-weight-bold text-white">{{ $p->score_a }}</div>
                                            </div>
                                            <div class="col-2 p-0">
                                                <div class="text-muted font-weight-bold small">VS</div>
                                            </div>
                                            <div class="col-5">
                                                <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">{{ $p->teamB?->name ?? 'TBD' }}</h4>
                                                <div class="h3 font-weight-bold text-white">{{ $p->score_b }}</div>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between align-items-center" style="border-color: rgba(255,255,255,0.05) !important;">
                                            <div class="small text-muted">
                                                <div class="d-flex align-items-center mb-1">
                                                    <i class="bi bi-calendar3 mr-2"></i> {{ \Carbon\Carbon::parse($p->waktu_tanding)->format('d M Y') }}
                                                </div>
                                            </div>
                                            <a href="{{ route('pertandingan.show', $p->id) }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                                                Detail
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

        @if($history->isEmpty() && $tournaments->isEmpty())
            <div class="card border-0 py-5 text-center" style="background: rgba(255,255,255,0.02); border-radius: 24px;">
                <div class="card-body">
                    <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                        style="width: 80px; height: 80px; background: rgba(255,255,255,0.05) !important;">
                        <i class="bi bi-archive text-muted h2 mb-0"></i>
                    </div>
                    <h3 class="font-weight-bold text-white">Belum Ada Riwayat</h3>
                    <p class="text-muted mx-auto" style="max-width: 400px;">Data pertandingan untuk periode ini belum tersedia.</p>
                </div>
            </div>
        @endif
    @endif
@endsection
