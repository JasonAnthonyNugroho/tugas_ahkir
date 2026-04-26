@extends('layouts.app')

@section('title', 'Live Tournament')

@section('content')
    {{-- Tournament Brackets --}}
    @if($tournaments->isNotEmpty())
        @foreach($tournaments as $tournament)
            <div class="mb-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center mr-3"
                        style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #a855f7) !important;">
                        <i class="bi bi-diagram-3 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-weight-bold mb-0">{{ $tournament->name }}</h3>
                        <p class="text-muted mb-0 small text-uppercase tracking-wider">{{ $tournament->sport->nama_sport }} •
                            {{ $tournament->year }}
                        </p>
                    </div>
                </div>

                <div class="bracket-wrapper overflow-auto pb-4">
                    <div class="d-flex" style="min-width: max-content;">
                        @php
                            $maxRound = $tournament->pertandingans->max('round');
                        @endphp

                        @for($r = 1; $r <= $maxRound; $r++)
                            @php
                                $roundMatches = $tournament->pertandingans->where('round', $r)->sortBy('match_number');
                            @endphp
                            <div class="bracket-round mr-5" style="width: 280px;">
                                <h5 class="text-center text-muted small font-weight-bold text-uppercase mb-4">
                                    {{ $roundMatches->first()->babak ?? 'Babak ' . $r }}
                                </h5>
                                <div class="d-flex flex-column justify-content-around h-100">
                                    @foreach($roundMatches as $match)
                                        <div class="bracket-match mb-4 p-3 rounded"
                                            style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); position: relative;">
                                            {{-- Tim A --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span
                                                    class="small font-weight-bold {{ $match->winner_id == $match->team_a_id && $match->team_a_id ? 'text-primary' : 'text-white' }}">
                                                    {{ $match->teamA?->name ?? 'TBD' }}
                                                </span>
                                                <span
                                                    class="badge {{ $match->winner_id == $match->team_a_id && $match->team_a_id ? 'badge-primary' : 'badge-dark' }} px-2">
                                                    {{ $match->score_a }}
                                                </span>
                                            </div>
                                            {{-- Tim B --}}
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span
                                                    class="small font-weight-bold {{ $match->winner_id == $match->team_b_id && $match->team_b_id ? 'text-primary' : 'text-white' }}">
                                                    {{ $match->teamB?->name ?? 'TBD' }}
                                                </span>
                                                <span
                                                    class="badge {{ $match->winner_id == $match->team_b_id && $match->team_b_id ? 'badge-primary' : 'badge-dark' }} px-2">
                                                    {{ $match->score_b }}
                                                </span>
                                            </div>

                                            {{-- Garis Penghubung (Hanya jika bukan final) --}}
                                            @if($r < $maxRound)
                                                <div class="bracket-connector"
                                                    style="position: absolute; right: -50px; top: 50%; width: 50px; height: 1px; background: var(--glass-border);">
                                                </div>

                                                {{-- Connector Vertical --}}
                                                @if($match->match_number % 2 != 0)
                                                    {{-- Match Ganjil: tarik garis ke bawah --}}
                                                    <div class="bracket-connector-v" style="top: 50%; height: calc(100% + 1.5rem);"></div>
                                                @else
                                                    {{-- Match Genap: tarik garis ke atas --}}
                                                    <div class="bracket-connector-v" style="bottom: 50%; height: calc(100% + 1.5rem);"></div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        @endforeach
        <hr class="border-secondary my-5" style="opacity: 0.1;">
    @endif

    <div class="mb-5">
        <h2 class="font-weight-bold mb-1">Live Tournaments</h2>
        <p class="text-muted">Pantau hasil pertandingan Rector Cup secara real-time.</p>
    </div>

    @if($pertandingans->isEmpty())
        <div class="card border-0 py-5 text-center" style="background: rgba(255,255,255,0.02);">
            <div class="card-body">
                <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                    style="width: 80px; height: 80px; background: rgba(255,255,255,0.05) !important;">
                    <i class="bi bi-calendar-x text-muted h2 mb-0"></i>
                </div>
                <h3 class="font-weight-bold">Belum Ada Pertandingan</h3>
                <p class="text-muted mx-auto" style="max-width: 400px;">Saat ini tidak ada pertandingan yang sedang berlangsung
                    atau dijadwalkan.</p>
            </div>
        </div>
    @else
        <div class="row" id="matchContainer">
            @foreach($pertandingans as $p)
                <div class="col-md-6 col-xl-4 mb-4 match-card" data-id="{{ $p->id }}">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <span class="badge-primary">
                                    <i class="bi {{ $p->sport->icon ?? 'bi-trophy' }} mr-2"></i>
                                    {{ $p->sport->nama_sport ?? 'Tournament' }}
                                </span>
                                <div class="badge-live-container">
                                    @if($p->status == 'live')
                                        <div class="badge-live">
                                            <span class="live-dot"></span> LIVE
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(strtoupper($p->sport->nama_sport ?? '') == 'PUBG MOBILE')
                                <div class="text-center py-3">
                                    <h5 class="text-muted small text-uppercase font-weight-bold mb-2">Total Points</h5>
                                    <div class="display-3 font-weight-bold text-white mb-2 score-a"
                                        style="text-shadow: 0 0 20px rgba(99, 102, 241, 0.3);">{{ $p->score_a }}</div>
                                    <h4 class="font-weight-bold text-uppercase tracking-wide">{{ $p->teamA?->name ?? 'TBD' }}</h4>
                                </div>
                            @else
                                <div class="row text-center align-items-center py-3">
                                    <div class="col-5">
                                        <h4 class="h6 font-weight-bold text-truncate mb-3">{{ $p->teamA?->name ?? 'TBD' }}</h4>
                                        <div class="display-4 font-weight-bold text-white score-a">{{ $p->score_a }}</div>
                                    </div>
                                    <div class="col-2 p-0">
                                        <div class="text-muted font-weight-bold small">VS</div>
                                    </div>
                                    <div class="col-5">
                                        <h4 class="h6 font-weight-bold text-truncate mb-3">{{ $p->teamB?->name ?? 'TBD' }}</h4>
                                        <div class="display-4 font-weight-bold text-white score-b">{{ $p->score_b }}</div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-geo-alt mr-2"></i> {{ $p->lokasi }}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 mr-2"></i>
                                        {{ \Carbon\Carbon::parse($p->waktu_tanding)->format('d M, H:i') }}
                                    </div>
                                </div>
                                <a href="{{ route('pertandingan.show', $p->id) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                    Detail <i class="bi bi-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        window.Echo.channel('scores')
            .listen('.score.updated', (e) => {
                const matchCard = document.querySelector(`.match-card[data-id="${e.id}"]`);
                if (matchCard) {
                    const scoreA = matchCard.querySelector('.score-a');
                    const scoreB = matchCard.querySelector('.score-b');
                    const badgeContainer = matchCard.querySelector('.badge-live-container');

                    if (scoreA) scoreA.innerText = e.score_a;
                    if (scoreB) scoreB.innerText = e.score_b;

                    if (e.status === 'live') {
                        badgeContainer.innerHTML = `<div class="badge-live"><span class="live-dot"></span> LIVE</div>`;
                    } else if (e.status === 'finished') {
                        matchCard.remove();
                        if (document.querySelectorAll('.match-card').length === 0) location.reload();
                    } else {
                        badgeContainer.innerHTML = '';
                    }

                    // Modern Highlight Effect
                    [scoreA, scoreB].forEach(el => {
                        if (el) {
                            el.style.color = '#818cf8';
                            el.style.transform = 'scale(1.1)';
                            el.style.transition = 'all 0.3s';
                            setTimeout(() => {
                                el.style.color = '';
                                el.style.transform = '';
                            }, 2000);
                        }
                    });
                }
            })
            .listen('.match.created', (e) => {
                if (document.querySelector('.bi-calendar-x')) {
                    location.reload();
                    return;
                }

                const container = document.getElementById('matchContainer');
                if (container) {
                    const isPubg = e.sport_nama.toUpperCase() === 'PUBG MOBILE';
                    const matchHtml = `
                                    <div class="col-md-6 col-xl-4 mb-4 match-card" data-id="${e.id}">
                                        <div class="card h-100 shadow-sm border-0">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-start mb-4">
                                                    <span class="badge-primary">
                                                        <i class="bi ${e.sport_icon} mr-2"></i>
                                                        ${e.sport_nama}
                                                    </span>
                                                    <div class="badge-live-container">
                                                        ${e.status === 'live' ? '<div class="badge-live"><span class="live-dot"></span> LIVE</div>' : ''}
                                                    </div>
                                                </div>
                                                ${isPubg ? `
                                                    <div class="text-center py-3">
                                                        <h5 class="text-muted small text-uppercase font-weight-bold mb-2">Total Points</h5>
                                                        <div class="display-3 font-weight-bold text-white mb-2 score-a">${e.score_a}</div>
                                                        <h4 class="font-weight-bold text-uppercase tracking-wide">${e.team_a_name}</h4>
                                                    </div>
                                                ` : `
                                                    <div class="row text-center align-items-center py-3">
                                                        <div class="col-5">
                                                            <h4 class="h6 font-weight-bold text-truncate mb-3">${e.team_a_name}</h4>
                                                            <div class="display-4 font-weight-bold text-white score-a">${e.score_a}</div>
                                                        </div>
                                                        <div class="col-2 p-0"><div class="text-muted font-weight-bold small">VS</div></div>
                                                        <div class="col-5">
                                                            <h4 class="h6 font-weight-bold text-truncate mb-3">${e.team_b_name}</h4>
                                                            <div class="display-4 font-weight-bold text-white score-b">${e.score_b}</div>
                                                        </div>
                                                    </div>
                                                `}
                                                <div class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between align-items-center">
                                                    <div class="small text-muted">
                                                        <div class="d-flex align-items-center mb-1"><i class="bi bi-geo-alt mr-2"></i> ${e.lokasi}</div>
                                                        <div class="d-flex align-items-center"><i class="bi bi-calendar3 mr-2"></i> ${e.waktu_tanding}</div>
                                                    </div>
                                                    <a href="${e.detail_url}" class="btn btn-primary btn-sm rounded-pill px-3">
                                                        Detail <i class="bi bi-arrow-right ml-1"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                    container.insertAdjacentHTML('beforeend', matchHtml);
                }
            });
    </script>
@endsection