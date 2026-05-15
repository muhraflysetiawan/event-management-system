<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Welcome') — {{ $appName }}</title>
    <meta name="description" content="@yield('description', 'University Event Management System')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            transition: none !important;
            animation: none !important;
            scroll-behavior: auto !important;
        }
    </style>
</head>
<body class="guest-body" x-data>
    <div class="guest-container">
        <div class="guest-card">
            <div class="guest-logo">
                @php $websiteLogo = \App\Models\Setting::get('website_logo'); @endphp
                @if($websiteLogo)
                    <img src="{{ asset('storage/' . $websiteLogo) }}" style="max-height:80px; width:auto; margin:0 auto 1.5rem; display:block; object-fit:contain;">
                @else
                    <div class="logo-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                @endif
                <h1>{{ $appName }}</h1>
                <p class="guest-subtitle">University Event Portal</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error" style="background:#fef2f2; color:#b91c1c; padding:1rem; border-radius:8px; border:1px solid #fee2e2; margin-bottom:1.5rem; font-size:0.875rem;">
                    <div style="display:flex; gap:0.75rem; align-items:flex-start;">
                        <i class="fas fa-exclamation-circle" style="margin-top:0.2rem;"></i>
                        <ul style="list-style:none; margin:0; padding:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>
