@extends('layouts.app')

@section('title', 'Dashboard Live')

@section('content')
    <div class="mb-4">
        <h2 class="font-weight-bold mb-1">Pertandingan Berlangsung</h2>
        <p class="text-muted">Pantau hasil pertandingan secara real-time.</p>
    </div>

    @if($pertandingans->isEmpty())
        <div class="card border-0 py-5 text-center" style="background: rgba(255,255,255,0.02); border-radius: 24px;">
            <div class="card-body">
                <div class="bg-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                    style="width: 80px; height: 80px; background: rgba(255,255,255,0.05) !important;">
                    <i class="bi bi-calendar-x text-muted h2 mb-0"></i>
                </div>
                <h5 class="font-weight-bold text-white">Tidak Ada Pertandingan</h5>
                <p class="text-muted mx-auto mb-0" style="max-width: 400px;">Saat ini tidak ada pertandingan yang sedang berlangsung atau terjadwal untuk filter ini.</p>
            </div>
        </div>
    @else
        <div class="row" id="matchContainer">
            @foreach($pertandingans as $p)
                <div class="col-md-6 col-xl-4 mb-4 match-card" data-id="{{ $p->id }}">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 24px; background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border) !important; transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <span class="badge badge-primary px-3 py-1" style="border-radius: 100px;">
                                    <i class="bi {{ $p->sport->icon ?? 'bi-trophy' }} mr-2"></i>
                                    {{ $p->sport->nama_sport ?? 'Tournament' }}
                                </span>
                                <div class="badge-live-container">
                                    @if($p->status == 'live')
                                        <div class="badge-live">
                                            <span class="live-dot"></span> LIVE
                                        </div>
                                    @else
                                        <span class="badge badge-dark px-3 py-1 text-uppercase" style="border-radius: 100px; background: rgba(255,255,255,0.05);">
                                            Terjadwal
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row text-center align-items-center py-3">
                                <div class="col-5">
                                    <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">{{ $p->teamA?->name ?? 'TBD' }}</h4>
                                    <div class="display-4 font-weight-bold text-white score-a">{{ $p->score_a }}</div>
                                </div>
                                <div class="col-2 p-0">
                                    <div class="text-muted font-weight-bold small">VS</div>
                                </div>
                                <div class="col-5">
                                    <h4 class="h6 font-weight-bold text-truncate mb-3 text-white">{{ $p->teamB?->name ?? 'TBD' }}</h4>
                                    <div class="display-4 font-weight-bold text-white score-b">{{ $p->score_b }}</div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top border-secondary d-flex justify-content-between align-items-center" style="border-color: rgba(255,255,255,0.05) !important;">
                                <div class="small text-muted">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="bi bi-geo-alt mr-2 text-primary"></i> {{ $p->lokasi }}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 mr-2 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($p->waktu_tanding)->format('d M, H:i') }}
                                    </div>
                                </div>
                                <a href="{{ route('pertandingan.show', $p->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
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

@section('styles')
<style>
    .match-card:hover .card {
        transform: translateY(-5px);
        background: rgba(255,255,255,0.06) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important;
    }

    .badge-live {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        padding: 4px 12px;
        border-radius: 100px;
        font-weight: bold;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .live-dot {
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        margin-right: 6px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }
</style>
@endsection
