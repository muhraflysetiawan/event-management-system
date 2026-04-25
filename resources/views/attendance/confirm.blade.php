@extends('layouts.app')
@section('title', 'Confirm Attendance')

@section('content')
<div class="card" style="max-width:500px;margin:0 auto;text-align:center;">
    <div class="card-body" style="padding:2rem;">
        <div style="font-size:3rem;margin-bottom:1rem;color:#980517;">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <h2 style="font-size:1.375rem;font-weight:700;margin-bottom:0.5rem;">Confirm Check-In</h2>
        <p style="color:var(--text-muted);margin-bottom:1.5rem;">You are about to check in for:</p>

        <div style="background:var(--bg-input);border:1px solid var(--border-color);border-radius:var(--radius-md);padding:1.25rem;margin-bottom:1.5rem;">
            <h3 style="font-weight:700;font-size:1.125rem;margin-bottom:0.5rem;">{{ $event->title }}</h3>
            <p style="color:var(--text-muted);font-size:0.875rem;">
                <i class="fas fa-calendar"></i> {{ $event->start_date->format('d M Y, H:i') }} •
                <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
            </p>
        </div>

        <form method="POST" action="{{ route('attendance.checkin') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <i class="fas fa-check"></i> Confirm Check-In
            </button>
        </form>
    </div>
</div>
@endsection
