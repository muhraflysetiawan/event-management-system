@extends('layouts.app')
@section('title', 'My Participants')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="participants-grid">
            @forelse($participants as $reg)
            <div class="event-card-premium" onclick="window.location='{{ route('events.show', $reg->event) }}'" style="cursor: pointer;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                    <div>
                        <span class="badge-status" style="background: rgba(255,255,255,0.2) !important;">{{ strtoupper($reg->status) }}</span>
                    </div>
                    <code style="font-size: 0.75rem; color: #FFFFFF !important; opacity: 0.8;">#{{ $reg->participant_number }}</code>
                </div>
                
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">
                    {{ $reg->event->title }}
                </h3>
                
                <div style="font-size: 0.85rem; color: #FFFFFF !important; margin-bottom: 1.5rem; opacity: 0.9;">
                    <div style="margin-bottom: 0.25rem;"><i class="fas fa-calendar-alt" style="width: 1.5rem;"></i> {{ $reg->event->start_date->format('d M Y, H:i') }}</div>
                    <div><i class="fas fa-history" style="width: 1.5rem;"></i> Registered: {{ $reg->created_at->format('d M Y') }}</div>
                </div>

                <div style="margin-top: auto; padding-top: 1rem; display: flex; justify-content: flex-end; opacity: 0.8; font-size: 0.75rem; font-weight: 600; color: #FFFFFF !important;">
                    Click for more details <i class="fas fa-chevron-right" style="margin-left: 0.5rem;"></i>
                </div>
            </div>
            @empty
            <div style="padding: 4rem 2rem; text-align: center; grid-column: 1 / -1;">
                <i class="fas fa-clipboard" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem;"></i>
                <h3 style="color: #4b5563;">No registrations yet</h3>
                <p style="color: #6b7280;">Browse available events to register</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary mt-2">Browse Events</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

<div class="pagination-wrapper" style="margin-top: 1.5rem;">{{ $participants->links() }}</div>
@endsection
