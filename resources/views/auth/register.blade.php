@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
<div class="auth-layout">
    <div class="auth-card">
        <span class="auth-card-eyebrow">Student registration</span>
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

            <div class="registration-name-grid">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input
                        id="first_name"
                        type="text"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}"
                        placeholder="Juan"
                        required
                        autofocus
                        autocomplete="given-name"
                        data-name-field
                    >
                    @error('first_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="middle_name" class="form-label">Middle Name <span class="optional">(Optional)</span></label>
                    <input
                        id="middle_name"
                        type="text"
                        name="middle_name"
                        value="{{ old('middle_name') }}"
                        class="form-control {{ $errors->has('middle_name') ? 'is-invalid' : '' }}"
                        placeholder="Santos"
                        autocomplete="additional-name"
                        data-name-field
                    >
                    @error('middle_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group registration-last-name">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input
                        id="last_name"
                        type="text"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}"
                        placeholder="Dela Cruz"
                        required
                        autocomplete="family-name"
                        data-name-field
                    >
                    @error('last_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

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
                    autocomplete="email"
                >
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Minimum 8 characters"
                    required
                    autocomplete="new-password"
                >
                @error('password') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    placeholder="Repeat your password"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:.5rem;">
                Create Account
            </button>
        </form>

        <div class="auth-footer-link">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-name-field]').forEach((field) => {
        field.addEventListener('input', () => {
            field.value = field.value
                .replace(/[^\p{L}\p{M} ]/gu, '')
                .replace(/ {2,}/g, ' ')
                .replace(/^ /, '');
        });
    });
</script>
@endpush
