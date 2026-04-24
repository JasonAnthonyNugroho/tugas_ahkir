@extends('layouts.app')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card bg-dark border-secondary shadow-lg">
                <div class="card-header border-secondary text-center">
                    <h4 class="text-white font-weight-bold mb-0">LOGIN PANITIA</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="text-secondary small font-weight-bold text-uppercase">Email</label>
                            <input type="email" name="email" class="form-control bg-dark border-secondary text-white"
                                placeholder="" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary small font-weight-bold text-uppercase">Password</label>
                            <input type="password" name="password" class="form-control bg-dark border-secondary text-white"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold mt-4">MASUK KE PANEL
                            ADMIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection