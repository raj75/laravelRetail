@extends('layouts.app')
@section('title', 'Register')
@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d7a6f, #065a52);">
    <div class="card shadow-lg border-0" style="width: 400px; border-radius: 16px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4" style="color:#0d7a6f">Create Account</h3>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-vyapar w-100">Register</button>
            </form>
            <p class="text-center mt-3 mb-0 small"><a href="{{ route('login') }}">Back to login</a></p>
        </div>
    </div>
</div>
@endsection
