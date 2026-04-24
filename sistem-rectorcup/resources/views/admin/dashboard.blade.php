@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="text-white mb-4">Panel Pengelolaan Rector Cup</h2>

    {{-- Panel Tambah Pertandingan di Atas --}}
    <div class="card bg-dark border-primary mb-5 shadow">
        <div class="card-header bg-primary text-white font-weight-bold">
            <i class="bi bi-plus-circle-fill mr-2"></i> Tambah Pertandingan Baru
        </div>
        <div class="card-body">
            <form action="{{ route('pertandingan.store') }}" method="POST">
                @csrf
                <div class="row text-white">
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold">TIM A (PRODI)</label>
                        <select name="team_a" class="form-control bg-dark text-white border-secondary">
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold">TIM B (PRODI)</label>
                        <select name="team_b" class="form-control bg-dark text-white border-secondary">
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold">WAKTU</label>
                        <input type="datetime-local" name="waktu" class="form-control bg-dark text-white border-secondary">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="small font-weight-bold">LOKASI</label>
                        <input type="text" name="lokasi" class="form-control bg-dark text-white border-secondary">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary px-4 mt-2">Simpan Jadwal</button>
            </form>
        </div>
    </div>

    {{-- Daftar Pertandingan untuk Update Skor --}}
    <div class="table-responsive">
        <table class="table table-dark table-hover border-secondary">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pertandingan</th>
                    <th>Skor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pertandingans as $p)
                <tr>
                    <td>{{ $p->waktu_tanding->format('d M, H:i') }}</td>
                    <td>{{ $p->teamA->name }} VS {{ $p->teamB->name }}</td>
                    <td>{{ $p->score_a }} - {{ $p->score_b }}</td>
                    <td>
                        <span class="badge {{ $p->status == 'live' ? 'badge-danger' : 'badge-secondary' }}">
                            {{ strtoupper($p->status) }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info">Update Skor</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
