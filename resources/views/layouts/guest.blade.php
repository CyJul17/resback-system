<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Submit Feedback') — ResBack</title>
    <meta name="description" content="Submit anonymous campus feedback securely. Your voice matters.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
<div class="guest-layout">

    {{-- ═══════ HEADER ═══════ --}}
    <header class="guest-header">
        <a href="{{ route('feedback.create') }}" class="brand">
            <div class="brand-icon">💬</div>
            ResBack
        </a>
        <div style="display:flex;align-items:center;gap:.5rem;">
            @auth
                <a href="{{ route('dashboard') }}" class="header-link">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="header-link">Admin Login</a>
            @endauth
        </div>
    </header>

    {{-- ═══════ MAIN ═══════ --}}
    <main class="guest-main">
        @yield('content')
    </main>

    {{-- ═══════ FOOTER ═══════ --}}
    <footer style="text-align:center;padding:1.25rem;font-size:.8rem;color:var(--gray-400);border-top:1px solid var(--gray-200);background:white;">
        © {{ date('Y') }} ResBack — CCIS Feedback System &nbsp;·&nbsp; Powered by XLM-RoBERTa Sentiment Analysis
    </footer>

</div>

@stack('scripts')
</body>
</html>
