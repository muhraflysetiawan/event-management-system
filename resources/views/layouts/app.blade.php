<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $appName }}</title>
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
    <style>
        /* Force Header Alignment and Styling */
        .sidebar-header, .topbar {
            height: 64px !important;
            min-height: 64px !important;
            max-height: 64px !important;
            background: linear-gradient(135deg, #980517, #473f3d) !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 1.5rem !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            overflow: hidden !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            opacity: 1 !important;
            filter: none !important;
            backdrop-filter: none !important;
        }
        
        .sidebar {
            top: 0 !important;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1) !important;
            z-index: 1001 !important;
        }

        .topbar {
            z-index: 1000 !important;
        }

        .app-body, .main-content {
            top: 0 !important;
            align-items: flex-start !important;
        }

        .header-bubble {
            position: absolute !important;
            border-radius: 50% !important;
            pointer-events: none !important;
            z-index: 0 !important;
        }

        .header-bubble-1 { 
            top: -20% !important; 
            left: -10% !important; 
            width: 100px !important; 
            height: 100px !important; 
            background: rgba(255,255,255,0.1) !important; 
        }

        .header-bubble-2 { 
            bottom: -10% !important; 
            right: -5% !important; 
            width: 150px !important; 
            height: 150px !important; 
            background: rgba(255,255,255,0.05) !important; 
        }

        .sidebar-logo, .topbar-left, .topbar-right, .sidebar-close {
            z-index: 1 !important;
            display: flex !important;
            align-items: center !important;
        }

        .page-title {
            margin: 0 !important;
            line-height: 1 !important;
        }

        /* ═══════════ EVENT CARD PREMIUM OVERRIDE ═══════════ */
        .event-card-premium {
            background: linear-gradient(135deg, #980517, #473f3d) !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3) !important;
            border: none !important;
            overflow: hidden !important;
            position: relative !important;
            padding: 1.5rem !important;
            display: flex !important;
            flex-direction: column !important;
            transition: all 0.3s ease-out !important;
            color: #FFFFFF !important;
            min-width: 0 !important; /* Prevent blowout */
            width: 100% !important;
            box-sizing: border-box !important;
        }

        @media (max-width: 768px) {
            .event-card-premium {
                padding: 1.25rem !important;
            }
        }

        /* FORCE WHITE ON EVERYTHING INSIDE PREMIUM CARD */
        .event-card-premium, 
        .event-card-premium div, 
        .event-card-premium span, 
        .event-card-premium p, 
        .event-card-premium h3, 
        .event-card-premium a, 
        .event-card-premium i,
        .event-card-premium small {
            color: #FFFFFF !important;
            fill: #FFFFFF !important;
            opacity: 1 !important;
        }

        .event-card-premium:hover {
            transform: translateY(-8px) !important;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4) !important;
        }

        .event-card-premium .badge-status {
            background: rgba(255, 255, 255, 0.2) !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            backdrop-filter: blur(4px) !important;
            padding: 4px 12px !important;
            border-radius: 20px !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
        }

        .event-card-premium::before {
            content: '' !important;
            position: absolute !important;
            width: 180px !important;
            height: 180px !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border-radius: 50% !important;
            top: -60px !important;
            right: -60px !important;
            z-index: 0 !important;
        }

        .event-card-premium::after {
            content: '' !important;
            position: absolute !important;
            width: 100px !important;
            height: 100px !important;
            background: rgba(255, 255, 255, 0.05) !important;
            border-radius: 50% !important;
            bottom: -30px !important;
            left: -30px !important;
            z-index: 0 !important;
        }


        /* ═══════════ GLOBAL ICON COLOR ═══════════ */
        /* Icons inside buttons/topbar/sidebar inherit parent color */
        .topbar i, .sidebar i { color: inherit; }
        a.btn:hover i, button:hover i { color: inherit; }

        /* Exception for Nav Toggles */
        .menu-toggle i, .sidebar-close i {
            color: #FFFFFF !important;
        }

        /* ═══════════ ALERT OVERRIDES ═══════════ */
        /* Only set base border-radius, individual classes handle colors */
        .alert {
            border-radius: 12px !important;
            padding: 1rem 1.25rem !important;
            margin-bottom: 1.5rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.75rem !important;
            font-weight: 600 !important;
        }
        .alert-success { background: rgba(16, 185, 129, 0.08) !important; border: 1px solid rgba(16, 185, 129, 0.3) !important; color: #059669 !important; }
        .alert-warning { background: rgba(245, 158, 11, 0.08) !important; border: 1px solid rgba(245, 158, 11, 0.3) !important; color: #d97706 !important; }
        .alert-error   { background: rgba(239, 68, 68, 0.08)  !important; border: 1px solid rgba(239, 68, 68, 0.3)  !important; color: #dc2626 !important; }
        .alert-info    { background: rgba(6, 182, 212, 0.08)  !important; border: 1px solid rgba(6, 182, 212, 0.3)  !important; color: #0891b2 !important; }

        /* Custom Modal Style */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }
        .modal-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            text-align: center;
        }

        /* ═══════════ NAVIGATION VISIBILITY ═══════════ */
        .menu-toggle, .sidebar-close { display: none !important; }

        .topbar {
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            width: 100% !important;
        }

        /* Sidebar Transition Classes */
        .sidebar-transition { transition: transform 0.3s ease-in-out !important; }
        .sidebar-hidden { transform: translateX(-100%) !important; }
        .sidebar-visible { transform: translateX(0) !important; }

        @media (max-width: 767px) {
            .menu-toggle { display: block !important; }
            .sidebar-close { display: block !important; }
            .app-body { display: block !important; width: 100% !important; }
            .main-content { margin-left: 0 !important; width: 100% !important; max-width: 100% !important; display: block !important; }
            .sidebar { 
                position: fixed !important; 
                height: 100dvh !important; 
                width: 280px !important;
                z-index: 2000 !important; 
                top: 0 !important; 
                left: 0 !important; 
                bottom: 0 !important;
                overflow-y: auto !important;
                background: #FFFFFF !important;
                box-shadow: 10px 0 30px rgba(0,0,0,0.2) !important;
            }
            .no-scroll { overflow: hidden !important; position: fixed !important; width: 100% !important; height: 100% !important; }
            .page-content { padding: 0.75rem !important; width: 100% !important; }
            .card { padding: 1.25rem !important; width: 100% !important; margin-bottom: 1.5rem !important; }
            .stats-grid, .events-grid, .participants-grid, .dashboard-grid { 
                display: grid !important;
                grid-template-columns: 1fr !important; 
                gap: 1.5rem !important; 
                width: 100% !important; 
            }
            .stat-card { padding: 1.25rem !important; width: 100% !important; }
            .table-container { width: 100% !important; overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; }
            .btn { white-space: normal !important; text-align: center !important; }
            .sidebar-footer { padding-bottom: 5rem !important; margin-top: auto !important; }
            .event-card-premium { border-radius: 16px !important; }
        }

        /* ═══════════ TABLET & DESKTOP STABILITY ═══════════ */
        @media (min-width: 768px) {
            .sidebar { transform: translateX(0) !important; position: fixed !important; display: flex !important; }
            .main-content { margin-left: var(--sidebar-width) !important; width: calc(100% - var(--sidebar-width)) !important; }
            .topbar { padding: 0 1.5rem !important; }
            .participants-grid { 
                display: grid !important;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)) !important;
                gap: 1.5rem !important;
            }
        }

        .card { overflow: hidden !important; }
        * { box-sizing: border-box !important; }
        html, body { 
            margin: 0; 
            padding: 0; 
            max-width: 100vw !important; 
            overflow-x: hidden !important;
            width: 100%;
            position: relative;
        }
        .no-scroll { 
            overflow: hidden !important; 
            position: fixed !important; 
            width: 100% !important; 
            height: 100% !important; 
            touch-action: none !important;
        }
    </style>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ sidebarOpen: window.innerWidth > 1024 }" 
      :class="{ 'no-scroll': sidebarOpen && window.innerWidth <= 1024 }"
      class="app-body">
    <!-- Sidebar -->
    <aside class="sidebar" 
           :class="sidebarOpen ? 'sidebar-visible' : 'sidebar-hidden'" 
           style="display: flex !important; position: fixed !important; height: 100dvh !important; top: 0 !important; left: 0 !important; z-index: 2000 !important; overflow: hidden !important;">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                @php $websiteLogo = \App\Models\Setting::get('website_logo'); @endphp
                @if($websiteLogo)
                    <img src="{{ asset('storage/' . $websiteLogo) }}" style="height:32px; width:32px; object-fit:contain; margin-right:0.5rem;">
                @else
                    <i class="fas fa-user-graduate"></i>
                @endif
                <span>{{ $appName }}</span>
            </div>
            <button type="button" class="sidebar-close" @click="sidebarOpen = false">
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

            @if(in_array(auth()->user()->role->slug ?? '', ['student', 'lecturer', 'staff', 'external']))
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
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->isCommittee() || auth()->user()->isHeadDepartment() || auth()->user()->isACOO() || in_array(auth()->user()->role->slug ?? '', ['lecturer', 'staff', 'external']))
                <div class="nav-divider">
                    <span>Management</span>
                </div>
                @if(auth()->user()->isHeadDepartment() || auth()->user()->isAdmin() || auth()->user()->isACOO())
                    <a href="{{ route('events.approvals') }}" class="nav-link {{ request()->routeIs('events.approvals') ? 'active' : '' }}">
                        <i class="fas fa-check-double"></i>
                        <span>Approvals</span>
                        @php
                            $pendingCount = \App\Models\Event::where('status', 'pending_approval')
                                ->whereJsonContains('required_approval_roles', auth()->user()->role->slug)
                                ->where(function($q) {
                                    $q->whereNull('approved_by_roles')
                                      ->orWhereJsonDoesntContain('approved_by_roles', auth()->user()->role->slug);
                                })->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif
                
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Manage Users</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>App Settings</span>
                </a>
                @endif
                <a href="{{ route('events.create') }}" class="nav-link {{ request()->routeIs('events.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i>
                    <span>Create Event</span>
                </a>
                @if(auth()->user()->isAdmin() || auth()->user()->isCommittee() || auth()->user()->isHeadDepartment())
                <a href="{{ route('participants.index') }}" class="nav-link {{ request()->routeIs('participants.index') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    <span>Manage Participants</span>
                </a>
                @endif
            @endif

            @if(auth()->user()->isLecturer())
                <a href="{{ route('profile.signature') }}" class="nav-link {{ request()->routeIs('profile.signature') ? 'active' : '' }}">
                    <i class="fas fa-pen-nib"></i>
                    <span>My Signature</span>
                </a>
            @endif

            <div class="nav-divider">
                <span>Account</span>
            </div>
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') && !request()->routeIs('profile.signature') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>My Profile</span>
            </a>
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

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen && window.innerWidth <= 1024" 
         x-transition.opacity
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-[1000] lg:hidden"
         style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    </div>

    <!-- Main Content -->
    <main class="main-content" :class="{ 'sidebar-expanded': sidebarOpen }">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="header-bubble header-bubble-1"></div>
            <div class="header-bubble header-bubble-2"></div>
            <div class="topbar-left" style="z-index: 1; display: flex; align-items: center; gap: 1rem;">
                <button type="button" class="menu-toggle" @click="sidebarOpen = !sidebarOpen">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 class="page-title">@yield('title', 'Dashboard')</h2>
            </div>
            <div class="topbar-right" style="z-index: 1;">
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
