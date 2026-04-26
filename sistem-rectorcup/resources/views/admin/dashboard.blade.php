@extends('layouts.app')

@section('title', 'Kelola Jadwal')

@section('content')
    <div class="mb-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h2 class="font-weight-bold mb-1">Manajemen Pertandingan</h2>
            <p class="text-muted">Kelola bracket, jadwal, dan aktivasi pertandingan Rector Cup.</p>
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

    {{-- Alert Section --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 py-3" style="border-radius: 16px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="bi bi-check-circle-fill mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Section Tournament/Bracket --}}
    <div class="mb-5">
        <h5 class="text-white font-weight-bold mb-4"><i class="bi bi-trophy text-warning mr-2"></i> Tournament & Bracket Aktif</h5>
        <div class="row">
            @forelse($tournaments as $tournament)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 h-100" style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border) !important; border-radius: 24px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center mr-3"
                                        style="width: 40px; height: 40px; background: linear-gradient(135deg, #6366f1, #a855f7) !important;">
                                        <i class="bi {{ $tournament->sport->icon ?? 'bi-diagram-3' }} text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold text-white mb-0">{{ $tournament->name }}</h6>
                                        <span class="text-muted small text-uppercase">{{ $tournament->sport->nama_sport }} • {{ $tournament->year }}</span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" data-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical h5 mb-0"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right bg-dark border-secondary shadow-lg">
                                        <button class="dropdown-item text-white small" onclick="previewBracket({{ $tournament->id }})">
                                            <i class="bi bi-eye mr-2"></i> Preview Bracket
                                        </button>
                                        <div class="dropdown-divider border-secondary"></div>
                                        <form action="{{ route('admin.bracket.reroll', $tournament->id) }}" method="POST" onsubmit="return confirm('Reroll akan mengacak ulang semua tim di bracket. Lanjutkan?')">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-warning small font-weight-bold">
                                                <i class="bi bi-shuffle mr-2"></i> Reroll Bracket
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Bulk Live Action for Tournament --}}
                            <form action="{{ route('pertandingan.bulk-live') }}" method="POST" id="bulkLiveForm{{ $tournament->id }}">
                                @csrf
                                <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-borderless text-white mb-0">
                                        <thead>
                                            <tr class="text-muted small text-uppercase">
                                                <th style="width: 30px;">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input checkAll" data-target="{{ $tournament->id }}" id="checkAll{{ $tournament->id }}">
                                                        <label class="custom-control-label" for="checkAll{{ $tournament->id }}"></label>
                                                    </div>
                                                </th>
                                                <th>Babak</th>
                                                <th>Pertandingan</th>
                                                <th>Status</th>
                                                <th class="text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $tMatches = $groupedMatches->get('tournament_' . $tournament->id, collect());
                                            @endphp
                                            @foreach($tMatches as $p)
                                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                                    <td>
                                                        @if($p->status == 'scheduled')
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="match_ids[]" value="{{ $p->id }}" class="custom-control-input match-checkbox-{{ $tournament->id }}" id="match_{{ $p->id }}">
                                                                <label class="custom-control-label" for="match_{{ $p->id }}"></label>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="small align-middle text-muted">{{ $p->babak }}</td>
                                                    <td class="small align-middle">
                                                        <span class="{{ $p->team_a_id ? 'text-white' : 'text-muted italic' }}">{{ $p->teamA?->name ?? 'TBD' }}</span>
                                                        <span class="text-muted mx-1">vs</span>
                                                        <span class="{{ $p->team_b_id ? 'text-white' : 'text-muted italic' }}">{{ $p->teamB?->name ?? 'TBD' }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        @if($p->status == 'live')
                                                            <span class="badge badge-success small">LIVE</span>
                                                        @elseif($p->status == 'finished')
                                                            <span class="badge badge-secondary small">DONE</span>
                                                        @else
                                                            <span class="badge badge-dark small">SCHED</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-right align-middle">
                                                        <button type="button" class="btn btn-link btn-sm text-primary p-0" data-toggle="modal" data-target="#editMatchModal{{ $p->id }}">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                                {{-- Modal Edit Individual Match --}}
                                                <div class="modal fade edit-match-modal" id="editMatchModal{{ $p->id }}" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content bg-dark border-secondary shadow-lg" style="border-radius: 24px; border: 1px solid rgba(255,255,255,0.1) !important;">
                                                            <form action="{{ route('pertandingan.quick-update', $p->id) }}" method="POST">
                                                                @csrf @method('PATCH')
                                                                <div class="modal-header border-0 p-4">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="bg-primary-subtle rounded-circle p-2 mr-3" style="background: rgba(99, 102, 241, 0.1);">
                                                                            <i class="bi bi-pencil-square text-primary"></i>
                                                                        </div>
                                                                        <h6 class="modal-title text-white font-weight-bold mb-0">Edit Detail: {{ $p->babak }}</h6>
                                                                    </div>
                                                                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                                                </div>
                                                                <div class="modal-body p-4">
                                                                    <div class="row">
                                                                        <div class="col-6 mb-3">
                                                                            <label class="small text-muted text-uppercase font-weight-bold mb-2">Tim A</label>
                                                                            <select name="team_a_id" class="form-control bg-dark text-white border-secondary select-no-focus">
                                                                                <option value="">TBD</option>
                                                                                @foreach($teams as $t)
                                                                                    <option value="{{ $t->id }}" {{ $p->team_a_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-6 mb-3">
                                                                            <label class="small text-muted text-uppercase font-weight-bold mb-2">Tim B</label>
                                                                            <select name="team_b_id" class="form-control bg-dark text-white border-secondary select-no-focus">
                                                                                <option value="">TBD</option>
                                                                                @foreach($teams as $t)
                                                                                    <option value="{{ $t->id }}" {{ $p->team_b_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-12 mb-3">
                                                                            <label class="small text-muted text-uppercase font-weight-bold mb-2">Waktu Tanding</label>
                                                                            <input type="datetime-local" name="waktu_tanding" class="form-control bg-dark text-white border-secondary input-no-focus" value="{{ $p->waktu_tanding->format('Y-m-d\TH:i') }}">
                                                                        </div>
                                                                        <div class="col-12 mb-3">
                                                                            <label class="small text-muted text-uppercase font-weight-bold mb-2">Lokasi</label>
                                                                            <input type="text" name="lokasi" class="form-control bg-dark text-white border-secondary input-no-focus" value="{{ $p->lokasi }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-0 p-4 pt-0">
                                                                    <button type="button" class="btn btn-link text-muted font-weight-bold text-decoration-none" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="border-radius: 12px;">Simpan Perubahan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm btn-block font-weight-bold py-2 rounded-pill shadow-sm" style="display: none;" id="btnBulkLive{{ $tournament->id }}">
                                    <i class="bi bi-play-fill mr-1"></i> Mulai Live Pertandingan Terpilih
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted text-center py-4 bg-dark-subtle rounded-xl" style="border: 1px dashed var(--glass-border);">Belum ada tournament aktif.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Section Independent Matches --}}
    <div class="mb-4">
        <h5 class="text-white font-weight-bold mb-3"><i class="bi bi-calendar-event text-primary mr-2"></i> Pertandingan Mandiri</h5>
        <div class="card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Sport</th>
                                <th>Waktu</th>
                                <th>Pertandingan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $iMatches = $groupedMatches->get('independent', collect()); @endphp
                            @forelse($iMatches->where('status', '!=', 'finished') as $p)
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
                                        <div class="text-white small font-weight-bold">{{ $p->waktu_tanding->format('d M, H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="font-weight-600 text-uppercase small text-white">
                                            {{ $p->teamA?->name ?? 'TBD' }} <span class="text-muted mx-2">VS</span> {{ $p->teamB?->name ?? 'TBD' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($p->status == 'live')
                                            <span class="badge-live"><span class="live-dot"></span> LIVE</span>
                                        @else
                                            <span class="badge badge-dark px-3 py-1">SCHEDULED</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($p->status == 'scheduled')
                                            <button class="btn btn-sm btn-success rounded-pill px-3 font-weight-bold" data-toggle="modal" data-target="#quickLive{{ $p->id }}">
                                                Mulai Live
                                            </button>
                                        @else
                                            <a href="{{ route('admin.skor') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 font-weight-bold">Update Skor</a>
                                        @endif
                                        
                                        {{-- Modal Quick Live same as before --}}
                                        <div class="modal fade" id="quickLive{{ $p->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content bg-dark border-secondary" style="border-radius: 24px;">
                                                    <form action="{{ url('/pertandingan/' . $p->id . '/update-score') }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="score_a" value="0">
                                                        <input type="hidden" name="score_b" value="0">
                                                        <input type="hidden" name="status" value="live">
                                                        <div class="modal-body text-center p-5">
                                                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1);">
                                                                <i class="bi bi-play-fill text-success h3 mb-0"></i>
                                                            </div>
                                                            <h5 class="text-white font-weight-bold mb-2">Aktivasi Live</h5>
                                                            <p class="text-muted mb-4">{{ $p->teamA?->name }} VS {{ $p->teamB?->name }}</p>
                                                            <button type="submit" class="btn btn-success btn-block py-3 font-weight-bold">MULAI SEKARANG</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted small">Tidak ada pertandingan mandiri aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
                            <div class="col-md-8 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Nama Tournament</label>
                                <input type="text" name="tournament_name" class="form-control"
                                    placeholder="Contoh: Rector Cup Futsal 2026" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Jumlah Tim</label>
                                <select name="manual_team_count" class="form-control">
                                    <option value="">Otomatis (Ikuti List Tim)</option>
                                    <option value="4">4 Tim</option>
                                    <option value="8">8 Tim</option>
                                    <option value="16">16 Tim</option>
                                </select>
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
                                <div class="alert alert-info py-2 px-3 mb-3 border-0 small" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa; border-radius: 12px;">
                                    <i class="bi bi-info-circle mr-1"></i> <b>Tips:</b> Jika ingin membuat bracket kosong (Manual Input), Anda bisa melewati pemilihan tim ini dan langsung klik Generate.
                                </div>
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
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="small font-weight-bold text-uppercase text-muted mb-2">Cabang Olahraga (Sport)</label>
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
                                <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Lapangan Basket UKDW" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-link text-muted font-weight-bold text-decoration-none" data-dismiss="modal">Batal</button>
                        <button type="button" onclick="confirmSave()" class="btn btn-primary px-5">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Preview Bracket --}}
    <div class="modal fade" id="previewBracketModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content bg-dark border-secondary" style="border-radius: 24px;">
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title text-white font-weight-bold">
                        <i class="bi bi-diagram-3 mr-2"></i> Preview Struktur Bracket
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-4 overflow-auto">
                    <div id="bracketPreviewContent" class="d-flex justify-content-center py-4">
                        {{-- Content loaded via JS --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.checkAll').on('change', function() {
                const target = $(this).data('target');
                const isChecked = $(this).prop('checked');
                $(`.match-checkbox-${target}`).prop('checked', isChecked);
                updateBulkBtn(target);
            });

            $('input[name="match_ids[]"]').on('change', function() {
                const target = $(this).closest('form').attr('id').replace('bulkLiveForm', '');
                updateBulkBtn(target);
            });

            function updateBulkBtn(target) {
                const checkedCount = $(`.match-checkbox-${target}:checked`).length;
                if (checkedCount > 0) {
                    $(`#btnBulkLive${target}`).fadeIn().text(`Mulai Live ${checkedCount} Pertandingan Terpilih`);
                } else {
                    $(`#btnBulkLive${target}`).fadeOut();
                }
            }

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
                }
            });
        });

        function previewBracket(tournamentId) {
            $('#bracketPreviewContent').html('<div class="spinner-border text-primary"></div>');
            $('#previewBracketModal').modal('show');
            
            // Simulating a quick view since we have the data in groupedMatches
            // In real app, you might fetch via AJAX or just filter from existing DOM
            const matches = @json($groupedMatches);
            const tMatches = matches['tournament_' + tournamentId] || [];
            
            if (tMatches.length === 0) {
                $('#bracketPreviewContent').html('<p class="text-muted">Data bracket tidak ditemukan.</p>');
                return;
            }

            let html = '<div class="d-flex align-items-start gap-4">';
            const rounds = [...new Set(tMatches.map(m => m.round))].sort((a,b) => a-b);
            
            rounds.forEach(round => {
                const roundMatches = tMatches.filter(m => m.round === round);
                html += `<div class="bracket-column d-flex flex-column justify-content-around" style="min-width: 200px; gap: 20px;">
                    <div class="text-center mb-3 small font-weight-bold text-uppercase text-primary">${roundMatches[0].babak}</div>`;
                
                roundMatches.forEach(m => {
                    const teamA = m.team_a ? m.team_a.name : 'TBD';
                    const teamB = m.team_b ? m.team_b.name : 'TBD';
                    html += `
                        <div class="p-3 rounded bg-dark border border-secondary shadow-sm mb-3">
                            <div class="small d-flex justify-content-between mb-1">
                                <span class="${m.team_a_id ? 'text-white' : 'text-muted'}">${teamA}</span>
                            </div>
                            <div class="border-top border-secondary my-1 opacity-25"></div>
                            <div class="small d-flex justify-content-between">
                                <span class="${m.team_b_id ? 'text-white' : 'text-muted'}">${teamB}</span>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            });
            html += '</div>';
            $('#bracketPreviewContent').html(html);
        }

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
