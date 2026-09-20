@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<div class="auth-layout">
    <div class="auth-card">
        <span class="auth-card-eyebrow">Secure account access</span>
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
                <label for="email" class="form-label">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
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
                <label for="password" class="form-label">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <p class="session-note">
                <span aria-hidden="true">◷</span> You will stay signed in for up to one hour.
            </p>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                Sign In
            </button>
        </form>

        <div class="auth-footer-link">
            Don't have an account?
            <a href="{{ route('register') }}">Create one</a>
        </div>

    </div>
</div>
@endsection
