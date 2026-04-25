<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — TMS</title>
    <meta name="description" content="@yield('description', 'Event Management System Dashboard')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            transition: none !important;
            animation: none !important;
            scroll-behavior: auto !important;
        }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: window.innerWidth > 1024 }" class="app-body">
    <!-- Sidebar -->
    <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-graduation-cap"></i>
                <span>TMS</span>
            </div>
            <button class="sidebar-close" @click="sidebarOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Events</span>
            </a>

            @if(auth()->user()->isStudent())
                <a href="{{ route('participants.my') }}" class="nav-link {{ request()->routeIs('participants.my') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>My Participants</span>
                </a>
                <a href="{{ route('attendance.scan') }}" class="nav-link {{ request()->routeIs('attendance.scan') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i>
                    <span>Scan QR</span>
                </a>
                <a href="{{ route('certificates.index') }}" class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                    <i class="fas fa-award"></i>
                    <span>Certificates</span>
                </a>
                <a href="{{ route('history.index') }}" class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isCommittee() || auth()->user()->isHeadDepartment())
                <div class="nav-divider">
                    <span>Management</span>
                </div>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>Audit Logs</span>
                </a>
                @endif
                <a href="{{ route('events.create') }}" class="nav-link {{ request()->routeIs('events.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Create Event</span>
                </a>
                <a href="{{ route('participants.index') }}" class="nav-link {{ request()->routeIs('participants.index') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Manage Participants</span>
                </a>
            @endif

            @if(auth()->user()->isLecturer())
                <a href="{{ route('profile.signature') }}" class="nav-link {{ request()->routeIs('profile.signature') ? 'active' : '' }}">
                    <i class="fas fa-pen-nib"></i>
                    <span>My Signature</span>
                </a>
                <a href="{{ route('history.index') }}" class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </a>
            @endif

            <div class="nav-divider">
                <span>Account</span>
            </div>
            <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                @php $unreadCount = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
                @if($unreadCount > 0)
                    <span class="badge">{{ $unreadCount }}</span>
                @endif
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="user-details">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ auth()->user()->role->name ?? 'User' }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" :class="{ 'sidebar-expanded': sidebarOpen }">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <h2 class="page-title">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="topbar-right">
                <a href="{{ route('notifications.index') }}" class="topbar-icon" x-data="notificationBell()" x-init="fetchCount()">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" x-show="count > 0" x-text="count" x-cloak></span>
                </a>
                <div class="topbar-user">
                    <span>{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <button @click="show = false" class="alert-close"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                    <button @click="show = false" class="alert-close"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button @click="show = false" class="alert-close"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" x-show="sidebarOpen && window.innerWidth <= 1024" @click="sidebarOpen = false" x-cloak></div>

    @stack('scripts')
</body>
</html>
