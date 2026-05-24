@extends('layouts.app')
@section('title', 'Login')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d7a6f, #065a52);">
    <div class="card shadow-lg border-0" style="width: 400px; border-radius: 16px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-1" style="color:#0d7a6f"><i class="bi bi-shop"></i> LaravelRetail</h3>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <button type="submit" class="btn btn-vyapar w-100">Login</button>
            </form>
            <p class="text-center mt-3 mb-0 small">No account? <a href="{{ route('register') }}">Register</a></p>
        </div>
    </div>
</div>
@endsection
