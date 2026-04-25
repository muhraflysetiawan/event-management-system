@extends('layouts.app')
@section('title', 'Attendance Result')

@section('content')
<div class="card" style="max-width:500px;margin:0 auto;text-align:center;">
    <div class="card-body" style="padding:2.5rem;">
        @if($success)
            <div style="width:80px;height:80px;background:var(--gradient-success);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
                <i class="fas fa-check" style="font-size:2rem;color:white;"></i>
            </div>
            <h2 style="font-size:1.375rem;font-weight:700;color:#6ee7b7;margin-bottom:0.5rem;">Check-In Successful!</h2>
        @else
            <div style="width:80px;height:80px;background:#980517;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
                <i class="fas fa-times" style="font-size:2rem;color:white;"></i>
            </div>
            <h2 style="font-size:1.375rem;font-weight:700;color:#980517;margin-bottom:0.5rem;">Check-In Failed</h2>
        @endif

        <p style="color:var(--text-muted);margin-bottom:1.5rem;">{{ $message }}</p>

        @if(isset($event))
        <div style="background:var(--bg-input);border:1px solid var(--border-color);border-radius:var(--radius-md);padding:1rem;margin-bottom:1.5rem;">
            <p style="font-weight:600;color:var(--text-primary);">{{ $event->title }}</p>
            <p style="color:var(--text-muted);font-size:0.8125rem;margin-top:0.25rem;">Checked in at {{ now()->format('H:i:s') }}</p>
        </div>
        @endif

        <a href="{{ route('dashboard') }}" class="btn btn-primary"><i class="fas fa-home"></i> Back to Dashboard</a>
    </div>
</div>
@endsection
