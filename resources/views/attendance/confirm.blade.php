@extends('layouts.app')
@section('title', 'Confirm Attendance')

@section('content')
<div class="event-card-premium" style="max-width:420px; margin: 3rem auto; padding: 2rem; text-align: center;">
    <div style="font-size: 3.5rem; margin-bottom: 1.25rem; color: #FFFFFF !important;">
        <i class="fas fa-user-check"></i>
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; color: #FFFFFF !important;">Confirm Attendance</h2>
    <p style="color: rgba(255, 255, 255, 0.8) !important; margin-bottom: 1.5rem; font-size: 0.9rem;">You are recording your presence for:</p>

    <div style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 1.25rem; margin-bottom: 2rem; text-align: left;">
        <h3 style="font-weight: 800; font-size: 1.1rem; margin-bottom: 0.5rem; color: #FFFFFF !important;">{{ $event->title }}</h3>
        <div style="color: rgba(255, 255, 255, 0.9) !important; font-size: 0.85rem; display: flex; flex-direction: column; gap: 0.4rem;">
            <span><i class="fas fa-calendar-alt" style="width: 1.2rem;"></i> {{ $event->start_date->format('d M Y') }}</span>
            <span><i class="fas fa-clock" style="width: 1.2rem;"></i> {{ $event->start_date->format('H:i') }} WIB</span>
        </div>
    </div>

    <form method="POST" action="{{ route('attendance.checkin') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <style>
            .btn-confirm-attendance {
                width: 100%;
                background: #FFFFFF !important;
                color: #980517 !important;
                border: none !important;
                padding: 0.875rem !important;
                font-size: 1rem !important;
                font-weight: 800 !important;
                border-radius: 10px !important;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .btn-confirm-attendance:hover {
                background: #980517 !important;
                color: #FFFFFF !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
        </style>
        <button type="submit" class="btn-confirm-attendance">
            <i class="fas fa-check-circle"></i> Confirm Attendance
        </button>
    </form>
    
    <div style="margin-top: 1.5rem;">
        <a href="{{ route('dashboard') }}" style="color: rgba(255, 255, 255, 0.6); text-decoration: none; font-size: 0.8rem; font-weight: 600;">Cancel</a>
    </div>
</div>
@endsection
