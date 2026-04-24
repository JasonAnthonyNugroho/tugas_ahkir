@extends('layouts.app')
@section('title', 'Live Tournament')
@section('content')

@if($pertandingans->isEmpty())
    <div class="text-center py-5">
        <h2 class="display-4 font-weight-bold mt-5">RECTOR CUP belum dimulai</h2>
        <p class="lead text-muted">Belum ada pertandingan yang dijadwalkan saat ini.</p>
    </div>
@else
    <div class="row">
        @foreach($pertandingans as $p)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge badge-primary p-2">RC</span>
                        @if($p->status == 'live')
                            <span class="text-danger font-weight-bold small"><span class="live-indicator"></span> LIVE</span>
                        @endif
                    </div>
                    <div class="row text-center align-items-center">
                        <div class="col-5">
                            <div class="font-weight-bold text-truncate">{{ $p->teamA->name }}</div>
                            <div class="h3 mt-2">{{ $p->score_a }}</div>
                        </div>
                        <div class="col-2 text-muted">VS</div>
                        <div class="col-5">
                            <div class="font-weight-bold text-truncate">{{ $p->teamB->name }}</div>
                            <div class="h3 mt-2">{{ $p->score_b }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="small text-muted text-center">
                        <i class="bi bi-geo-alt-fill"></i> {{ $p->lokasi }} <br>
                        <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($p->waktu_tanding)->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection