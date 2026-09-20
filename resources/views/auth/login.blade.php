@extends('layouts.auth')
@section('title', 'Sign In')

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

        <h2 class="auth-title">Welcome back</h2>
        <p class="auth-subtitle">Sign in to submit feedback or access the faculty dashboard.</p>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Errors --}}
        @if($errors->any())
            <div class="alert alert-error">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

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
                    autofocus
                    autocomplete="email"
                >
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label form-label-dark">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control form-control-dark {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <p style="font-size:.8rem;color:var(--gray-400);margin-bottom:1.5rem;">
                You will stay signed in for up to one hour.
            </p>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                🔐 Sign In
            </button>
        </form>

        <div class="auth-footer-link">
            Don't have an account?
            <a href="{{ route('register') }}">Create one</a>
        </div>

    </div>
</div>
@endsection
