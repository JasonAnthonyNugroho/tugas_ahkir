@extends('layouts.app')

@section('content')
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-5">
                <div class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; background: rgba(99, 102, 241, 0.1);">
                    <i class="bi bi-shield-lock text-primary h1 mb-0"></i>
                </div>
                <h2 class="font-weight-bold text-white">Rector Cup</h2>
                <p class="text-muted">Silakan masuk ke Panel Administrasi</p>
            </div>

            <div class="card border-0 shadow-lg" style="background: var(--bg-dark); border: 1px solid var(--glass-border) !important; border-radius: 24px;">
                <div class="card-body p-5">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-uppercase text-muted mb-2">Username</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-right-0" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 12px 0 0 12px; color: var(--text-muted);">
                                        <i class="bi bi-person"></i>
                                    </span>
                                </div>
                                <input type="text" name="username" class="form-control" 
                                    style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 0 12px 12px 0; color: white;"
                                    placeholder="Masukkan username" required>
                            </div>
                        </div>

                        <div class="form-group mb-5">
                            <label class="small font-weight-bold text-uppercase text-muted mb-2">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-right-0" style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 12px 0 0 12px; color: var(--text-muted);">
                                        <i class="bi bi-key"></i>
                                    </span>
                                </div>
                                <input type="password" name="password" class="form-control" 
                                    style="background: rgba(15, 23, 42, 0.5); border: 1px solid var(--glass-border); border-radius: 0 12px 12px 0; color: white;"
                                    placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block py-3 font-weight-bold" 
                            style="border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary)); border: none; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
                            MASUK SEKARANG <i class="bi bi-arrow-right ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left mr-1"></i> Kembali ke Dashboard Publik
                </a>
            </div>
        </div>
    </div>

    <style>
        .min-vh-75 {
            min-height: 75vh;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.7) !important;
            border-color: var(--accent-primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
            color: white !important;
        }
        .input-group:focus-within .input-group-text {
            border-color: var(--accent-primary) !important;
            color: var(--accent-primary) !important;
        }
    </style>
@endsection