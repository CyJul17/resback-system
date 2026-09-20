<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — ResBack Admin</title>
    <meta name="description" content="ResBack administration dashboard for managing student feedback and sentiment analytics.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
<div class="app-layout">

    {{-- ═══════ SIDEBAR ═══════ --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">💬</div>
            <div>
                <div class="brand-text">ResBack</div>
                <div class="brand-sub">Feedback System</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                Dashboard
            </a>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('accounts.index') }}"
                   class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span>
                    Manage Accounts
                </a>
            @endif

            @if(auth()->user()->isFaculty())
                <div class="nav-section-label" style="margin-top:.75rem;">Quick Actions</div>

                <a href="{{ route('feedback.create') }}" target="_blank" class="nav-link">
                    <span class="nav-icon">📝</span>
                    View Feedback Form
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="user-info" style="flex:1; min-width:0;">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">{{ auth()->user()->role }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout"
                        style="background:none;border:none;cursor:pointer;color:var(--gray-500);font-size:1.1rem;padding:.25rem;line-height:1;transition:color .15s;"
                        onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--gray-500)'">
                        🚪
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ═══════ MAIN CONTENT ═══════ --}}
    <div class="app-content">
        <header class="app-topbar">
            <div>
                <h1>@yield('page-title', 'Dashboard')</h1>
                <div class="topbar-sub">@yield('page-subtitle', 'ResBack Feedback System')</div>
            </div>
            <div style="display:flex;align-items:center;gap:.75rem;">
                @yield('topbar-actions')
            </div>
        </header>

        <main class="app-body">
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">❌ {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>
