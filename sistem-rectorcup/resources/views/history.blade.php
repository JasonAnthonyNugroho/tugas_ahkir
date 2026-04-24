@extends('layouts.app')

@section('title', 'Riwayat Pertandingan')

@section('content')
    <div class="container pb-5">
        <h2 class="text-white mb-4 font-weight-bold">
            <i class="bi bi-clock-history mr-2 text-primary"></i> Arsip Rector Cup
        </h2>

        @if($history->isEmpty())
            <div class="text-center py-5">
                <div class="display-1 text-muted"><i class="bi bi-archive"></i></div>
                <h3 class="text-muted mt-3">Belum ada riwayat pertandingan.</h3>
            </div>
        @else
            {{-- Perulangan Pertama: Mengambil Tahun --}}
            @foreach($history as $tahun => $daftarPertandingan)
                <div class="year-section mt-5">
                    <div class="d-flex align-items-center mb-4">
                        <h3 class="text-white font-weight-bold mb-0">Tahun {{ $tahun }}</h3>
                        <div class="flex-grow-1 ml-3 border-bottom border-secondary"></div>
                    </div>

                    <div class="row">
                        {{-- Perulangan Kedua: Menampilkan Pertandingan di Tahun Tersebut --}}
                        @foreach($daftarPertandingan as $p)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card bg-dark border-secondary shadow-sm rounded-lg h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="badge badge-secondary px-2 py-1 text-uppercase" style="font-size: 0.7rem;">
                                                {{ $p->kategori ?? 'Umum' }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $p->selesai_pada ? $p->selesai_pada->diffForHumans() : 'Selesai' }}
                                            </small>
                                        </div>

                                        <div class="row text-center align-items-center no-gutters">
                                            <div class="col-5">
                                                <div class="text-white font-weight-bold small text-uppercase">{{ $p->teamA->name }}
                                                </div>
                                                <div class="h2 text-white mt-2 mb-0">{{ $p->score_a }}</div>
                                            </div>
                                            <div class="col-2">
                                                <div class="text-muted font-weight-bold">-</div>
                                            </div>
                                            <div class="col-5">
                                                <div class="text-white font-weight-bold small text-uppercase">{{ $p->teamB->name }}
                                                </div>
                                                <div class="h2 text-white mt-2 mb-0">{{ $p->score_b }}</div>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-top border-secondary">
                                            <div class="text-muted small mb-3">
                                                <i class="bi bi-geo-alt-fill text-danger mr-1"></i> {{ $p->lokasi }}
                                            </div>
                                            <a href="#" class="btn btn-outline-primary btn-block btn-sm rounded-pill font-weight-bold">
                                                Lihat Detail Hasil
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
    </div>
@endsection