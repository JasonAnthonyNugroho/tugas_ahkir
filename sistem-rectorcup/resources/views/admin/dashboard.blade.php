@extends('layouts.app')

@section('title', 'Kelola Jadwal')

@section('content')
    <div class="mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h2 class="font-weight-bold mb-1">Daftar Jadwal Pertandingan</h2>
            <p class="text-muted">Manajemen jadwal dan publikasi pertandingan Rector Cup.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary shadow-sm font-weight-bold px-4 py-2 mt-3 mt-md-0 mr-2"
                data-toggle="modal" data-target="#generateBracketModal">
                <i class="bi bi-diagram-3 mr-2"></i> Buat Bracket
            </button>
            <button type="button" class="btn btn-primary shadow-sm font-weight-bold px-4 py-2 mt-3 mt-md-0"
                data-toggle="modal" data-target="#addMatchModal">
                <i class="bi bi-plus-lg mr-2"></i> Tambah Pertandingan
            </button>
        </div>
    </div>

    {{-- Modal Buat Bracket --}}
    <div class="modal fade" id="generateBracketModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content"
                style="background: var(--bg-dark); border: 1px solid var(--glass-border); border-radius: 24px;">
                <div class="modal-header border-0 p-4"
                    style="background: linear-gradient(135deg, #6366f1, #a855f7) !important; border-radius: 24px 24px 0 0;">
                    <h5 class="modal-title text-white font-weight-bold">
                        <i class="bi bi-diagram-3 mr-2"></i> Buat Bracket Tournament
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form action="{{ route('admin.bracket.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Nama Tournament</label>
                                <input type="text" name="tournament_name" class="form-control"
                                    placeholder="Contoh: Rector Cup Futsal 2026" required>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Cabang Olahraga</label>
                                <select name="sport_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Cabang Sport...</option>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->id }}">{{ $sport->nama_sport }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Pilih Tim Peserta
                                    (Pilih Minimal 2)</label>
                                <div class="p-3 rounded"
                                    style="background: rgba(255,255,255,0.05); max-height: 300px; overflow-y: auto; border: 1px solid var(--glass-border);">
                                    <div class="row">
                                        @foreach($teams as $team)
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="team_ids[]" value="{{ $team->id }}"
                                                        class="custom-control-input" id="team_bracket_{{ $team->id }}">
                                                    <label class="custom-control-label text-white small"
                                                        for="team_bracket_{{ $team->id }}">
                                                        {{ $team->name }} <span
                                                            class="text-muted ml-1">({{ $team->prodi }})</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-link text-muted font-weight-bold text-decoration-none"
                            data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-5">Generate Bracket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pertandingan --}}
    <div class="modal fade" id="addMatchModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content"
                style="background: var(--bg-dark); border: 1px solid var(--glass-border); border-radius: 24px;">
                <div class="modal-header border-0 p-4"
                    style="background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)) !important; border-radius: 24px 24px 0 0;">
                    <h5 class="modal-title text-white font-weight-bold">
                        <i class="bi bi-calendar-plus mr-2"></i> Input Jadwal Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="formTambahJadwal" action="{{ route('pertandingan.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert mb-4"
                            style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); color: #a5b4fc; border-radius: 12px;">
                            <i class="bi bi-info-circle-fill mr-2"></i> <strong class="text-white">Tips:</strong> Pilih
                            Cabang Olahraga terlebih dahulu untuk menyesuaikan format input.
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Cabang Olahraga
                                    (Sport)</label>
                                <select name="sport_id" id="sportSelect" class="form-control" required>
                                    <option value="" disabled selected>Pilih Cabang Sport...</option>
                                    @foreach($sports as $sport)
                                        <option value="{{ $sport->id }}" data-nama="{{ strtoupper($sport->nama_sport) }}">
                                            {{ $sport->nama_sport }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4 team-a-container">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Tim A (Prodi)</label>
                                <select name="team_a" id="teamASelect" class="form-control" required>
                                    <option value="" disabled selected>Pilih Tim A...</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4 team-b-container">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Tim B (Prodi)</label>
                                <select name="team_b" id="teamBSelect" class="form-control" required>
                                    <option value="" disabled selected>Pilih Tim B...</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Waktu Tanding</label>
                                <input type="datetime-local" name="waktu" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Lokasi / GOR</label>
                                <input type="text" name="lokasi" class="form-control"
                                    placeholder="Contoh: Lapangan Basket UKDW" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-link text-muted font-weight-bold text-decoration-none"
                            data-dismiss="modal">Batal</button>
                        <button type="button" onclick="confirmSave()" class="btn btn-primary px-5">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tabel Pengelolaan --}}
    <div class="card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Sport</th>
                            <th>Waktu</th>
                            <th>Pertandingan</th>
                            <th class="text-center">Skor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pertandingans as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center mr-3"
                                            style="width: 32px; height: 32px; background: rgba(99, 102, 241, 0.1) !important;">
                                            <i class="bi {{ $p->sport->icon ?? 'bi-trophy' }} text-primary small"></i>
                                        </div>
                                        <span class="font-weight-600">{{ $p->sport->nama_sport ?? 'Tournament' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white small font-weight-bold">{{ $p->waktu_tanding->format('d M') }}</div>
                                    <div class="text-muted small">{{ $p->waktu_tanding->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    <div class="font-weight-600 text-uppercase small">
                                        @if(strtoupper($p->sport->nama_sport ?? '') == 'PUBG MOBILE')
                                            {{ $p->teamA?->name ?? 'TBD' }} <span class="badge badge-primary ml-2"
                                                style="font-size: 0.6rem;">BATTLE ROYALE</span>
                                        @else
                                            <span class="text-white">{{ $p->teamA?->name ?? 'TBD' }}</span>
                                            <span class="text-muted mx-2">VS</span>
                                            <span class="text-white">{{ $p->teamB?->name ?? 'TBD' }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="h5 font-weight-bold mb-0 text-primary">
                                        @if(strtoupper($p->sport->nama_sport ?? '') == 'PUBG MOBILE')
                                            {{ $p->score_a }} <small class="text-muted">PTS</small>
                                        @else
                                            {{ $p->score_a }} - {{ $p->score_b }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($p->status == 'live')
                                        <div class="badge-live">
                                            <span class="live-dot"></span> LIVE
                                        </div>
                                    @elseif($p->status == 'finished')
                                        <span class="badge px-3 py-1 text-success"
                                            style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 100px; font-size: 0.7rem; font-weight: 700;">
                                            FINISHED
                                        </span>
                                    @else
                                        <span class="badge px-3 py-1 text-muted"
                                            style="background: rgba(148, 163, 184, 0.1); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 100px; font-size: 0.7rem; font-weight: 700;">
                                            SCHEDULED
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const selprodiId = "{{ \App\Models\Team::where('name', 'Seluruh Prodi')->first()->id ?? '' }}";

            $('#sportSelect').on('change', function () {
                const selectedSport = $(this).find(':selected').data('nama');

                if (selectedSport === 'PUBG MOBILE') {
                    $('#teamASelect').val(selprodiId).trigger('change');
                    $('#teamBSelect').val(selprodiId).trigger('change');
                    $('.team-b-container').hide();
                    $('.team-a-container label').text('Format Pertandingan');
                } else {
                    $('.team-b-container').show();
                    $('.team-a-container label').text('Tim A (Prodi)');
                    if ($('#teamASelect').val() == selprodiId) {
                        $('#teamASelect').val('');
                        $('#teamBSelect').val('');
                    }
                }
            });
        });

        function confirmSave() {
            Swal.fire({
                title: 'Konfirmasi Jadwal',
                text: "Apakah data pertandingan sudah sesuai dan siap dipublikasikan?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Cek Kembali',
                background: '#1a1a1a',
                color: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formTambahJadwal').submit();
                }
            })
        }
    </script>
@endsection