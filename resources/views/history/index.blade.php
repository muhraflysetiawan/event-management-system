@extends('layouts.app')
@section('title', 'Event History')

@section('content')
@forelse($participants as $reg)
<div class="card mb-2" style="display:flex;flex-direction:row;align-items:center;gap:1.5rem;padding:1.25rem 1.5rem;">
    <div style="width:48px;height:48px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;
        background:{{ match($reg->event_status) {
            'certificate_available' => 'var(--gradient-primary)',
            'completed' => 'var(--gradient-success)',
            'ongoing' => 'var(--gradient-info)',
            default => 'var(--gradient-warning)'
        } }};">
        <i class="fas fa-{{ match($reg->event_status) {
            'certificate_available' => 'award',
            'completed' => 'check',
            'ongoing' => 'spinner fa-spin',
            default => 'clock'
        } }}" style="color:white;font-size:1.125rem;"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <h4 style="font-weight:700;font-size:1rem;margin-bottom:0.25rem;">{{ $reg->event->title }}</h4>
        <div style="font-size:0.8125rem;color:var(--text-muted);">
            <i class="fas fa-calendar"></i> {{ $reg->event->start_date->format('d M Y') }}
            <span style="margin:0 0.5rem;">•</span>
            <i class="fas fa-map-marker-alt"></i> {{ $reg->event->location }}
        </div>
    </div>
    <div style="text-align:right;flex-shrink:0;">
        <span class="badge-status badge-{{ $reg->event_status }}">
            {{ match($reg->event_status) {
                'certificate_available' => 'Certificate Available',
                'completed' => 'Completed',
                'ongoing' => 'Ongoing',
                default => ucfirst($reg->event_status)
            } }}
        </span>
        @if($reg->certificate)
        <div style="margin-top:0.5rem;">
            <a href="{{ route('certificates.download', $reg->certificate) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-download"></i> Download
            </a>
        </div>
        @endif
    </div>
</div>
@empty
<div class="card">
    <div class="empty-state" style="padding:3rem;">
        <i class="fas fa-history"></i>
        <h3>No event history</h3>
        <p>Your completed events will appear here</p>
        <a href="{{ route('events.index') }}" class="btn btn-primary mt-2">Browse Events</a>
    </div>
</div>
@endforelse
@endsection
