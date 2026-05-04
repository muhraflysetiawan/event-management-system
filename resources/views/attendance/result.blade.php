@extends('layouts.app')
@section('title', 'Attendance Result')

@section('content')
<div class="event-card-premium" style="max-width:420px; margin: 3rem auto; padding: 2.5rem; text-align: center;">
    @if($success)
        <div style="font-size: 4rem; margin-bottom: 1.5rem; color: #FFFFFF !important;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem; color: #FFFFFF !important;">Attendance Successful!</h2>
        
        @if(isset($event))
            <div style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem;">
                <h3 style="font-weight: 800; font-size: 1.35rem; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $event->title }}</h3>
                <p style="color: rgba(255, 255, 255, 0.9) !important; font-size: 1rem; font-weight: 600;">
                    Checked in at {{ now()->format('H:i:s') }}
                </p>
            </div>
        @endif
    @else
        <div style="font-size: 4rem; margin-bottom: 1.5rem; color: #FFFFFF !important;">
            <i class="fas fa-times-circle"></i>
        </div>
        <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem; color: #FFFFFF !important;">Attendance Failed</h2>
        <p style="color: rgba(255, 255, 255, 0.8) !important; margin-bottom: 2rem;">{{ $message }}</p>
    @endif

    <div style="margin-top: 1rem;">
        <style>
            .btn-result-home {
                display: inline-block;
                width: 100%;
                background: #FFFFFF !important;
                color: #980517 !important;
                border: none !important;
                padding: 1rem !important;
                font-size: 1.1rem !important;
                font-weight: 800 !important;
                border-radius: 12px !important;
                text-decoration: none;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            .btn-result-home:hover {
                background: #980517 !important;
                color: #FFFFFF !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                transform: translateY(-2px);
            }
        </style>
        <a href="{{ route('dashboard') }}" class="btn-result-home">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection
