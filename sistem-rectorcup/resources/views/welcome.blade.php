@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')
    <div class="row align-items-center min-vh-75">
        <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="badge-live mb-3" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); border-color: rgba(99, 102, 241, 0.3);">
                <i class="bi bi-stars mr-2"></i> RECTOR CUP 2026 IS HERE
            </div>
            <h1 class="display-3 font-weight-bold text-white mb-4" style="line-height: 1.1; letter-spacing: -2px;">
                Pantau Skor <br>
                <span style="background: linear-gradient(to right, var(--accent-primary), var(--accent-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Real-Time
                </span> 
                di Sini.
            </h1>
            <p class="lead text-muted mb-5" style="font-size: 1.25rem; max-width: 500px;">
                Dapatkan update skor terkini, jadwal pertandingan, dan riwayat kemenangan prodi favoritmu dalam satu platform modern.
            </p>
            <div class="d-flex flex-column flex-sm-row">
                <a href="{{ route('home') }}" class="btn btn-primary px-5 py-3 font-weight-bold mb-3 mb-sm-0 mr-sm-3 shadow-lg" style="border-radius: 16px;">
                    LIHAT SKOR LIVE <i class="bi bi-arrow-right ml-2"></i>
                </a>
                <a href="{{ route('history') }}" class="btn btn-outline-light px-5 py-3 font-weight-bold" style="border-radius: 16px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.05);">
                    RIWAYAT JUARA
                </a>
            </div>
            
            <div class="mt-5 d-flex align-items-center">
                <div class="d-flex -space-x-2">
                    <div class="rounded-circle border border-dark bg-secondary d-flex align-items-center justify-content-center text-white small" style="width: 40px; height: 40px; margin-right: -10px; border: 2px solid var(--bg-dark) !important;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="rounded-circle border border-dark bg-primary d-flex align-items-center justify-content-center text-white small" style="width: 40px; height: 40px; margin-right: -10px; border: 2px solid var(--bg-dark) !important;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="rounded-circle border border-dark bg-info d-flex align-items-center justify-content-center text-white small" style="width: 40px; height: 40px; border: 2px solid var(--bg-dark) !important;">
                        <i class="bi bi-plus"></i>
                    </div>
                </div>
                <span class="ml-3 text-muted small">Bergabung dengan <strong>500+</strong> mahasiswa lainnya</span>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="position-relative">
                <!-- Decorative Elements -->
                <div class="position-absolute" style="top: -50px; right: -50px; width: 300px; height: 300px; background: var(--accent-primary); filter: blur(120px); opacity: 0.2; z-index: -1;"></div>
                <div class="position-absolute" style="bottom: -50px; left: -50px; width: 300px; height: 300px; background: var(--accent-secondary); filter: blur(120px); opacity: 0.2; z-index: -1;"></div>
                
                <!-- Mockup Card -->
                <div class="card border-0 shadow-2xl p-2" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid var(--glass-border) !important; transform: perspective(1000px) rotateY(-5deg) rotateX(5deg); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="badge-live"><span class="live-dot"></span> LIVE NOW</span>
                            <span class="text-muted small">Final Basketball</span>
                        </div>
                        <div class="d-flex justify-content-around align-items-center py-4">
                            <div class="text-center">
                                <div class="bg-dark rounded-circle p-3 mb-3 d-inline-block" style="width: 70px; height: 70px; border: 1px solid var(--glass-border);">
                                    <i class="bi bi-shield-shaded h2 text-primary"></i>
                                </div>
                                <h5 class="font-weight-bold mb-0">TI</h5>
                            </div>
                            <div class="text-center">
                                <h1 class="display-4 font-weight-bold mb-0">2 : 1</h1>
                                <span class="text-muted small font-weight-bold">QUARTER 3</span>
                            </div>
                            <div class="text-center">
                                <div class="bg-dark rounded-circle p-3 mb-3 d-inline-block" style="width: 70px; height: 70px; border: 1px solid var(--glass-border);">
                                    <i class="bi bi-shield-shaded h2 text-secondary"></i>
                                </div>
                                <h5 class="font-weight-bold mb-0">SI</h5>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-top border-secondary">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">Recent Action: <strong>3pt shot by Budi (TI)</strong></div>
                                <div class="small text-primary font-weight-bold">View Details <i class="bi bi-chevron-right ml-1"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .min-vh-75 {
            min-height: 75vh;
        }
        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .-space-x-2 > * {
            margin-left: -0.5rem;
        }
    </style>
@endsection
