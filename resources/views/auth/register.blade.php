@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
<div class="auth-layout">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo">
            <div class="logo-icon">💬</div>
            <div>
                <div class="logo-text">ResBack</div>
                <div class="logo-sub">CCIS Feedback System</div>
            </div>
        </div>

        <h2 class="auth-title">Create an account</h2>
        <p class="auth-subtitle">Create a student account to submit and track your feedback session.</p>

        {{-- Errors --}}
        @if($errors->any())
            <div class="alert alert-error">
                <span>⚠️</span>
                <ul style="list-style:none;padding:0;margin:0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label form-label-dark">Full Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control form-control-dark {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    placeholder="Juan dela Cruz"
                    required
                    autofocus
                    autocomplete="name"
                >
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label form-label-dark">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control form-control-dark {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    placeholder="you@example.com"
                    required
                    autocomplete="email"
                >
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label form-label-dark">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control form-control-dark {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Minimum 8 characters"
                    required
                    autocomplete="new-password"
                >
                @error('password') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label form-label-dark">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control form-control-dark"
                    placeholder="Repeat your password"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:.5rem;">
                ✅ Create Account
            </button>
        </form>

        <div class="auth-footer-link">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </div>

    </div>
</div>
@endsection
