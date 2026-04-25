@extends('layouts.app')
@section('title', 'Events')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="filter-bar">
            <form method="GET" action="{{ route('events.index') }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;width:100%;align-items:center;">
                <input type="text" name="search" class="form-input" placeholder="Search events..." value="{{ request('search') }}" style="max-width:300px;">
                <select name="status" class="form-input" style="max-width:180px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['draft','pending_approval','approved','published','ongoing','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
                
                @if(auth()->user()->isAdmin() || auth()->user()->isCommittee())
                    <div style="margin-left: auto;">
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">Create Event</a>
                    </div>
                @endif
            </form>
        </div>

    <div class="training-grid">
        @forelse($events as $event)
        <div class="training-card">
            <a href="{{ route('events.show', $event) }}" style="text-decoration: none; color: inherit; display: block;">
                <div class="training-card-header">
                    <span class="badge-status badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                </div>
                <div class="training-card-body">
                    <h3 class="training-title" style="font-size: 1.25rem;">{{ $event->title }}</h3>
                    <p class="training-desc">{{ Str::limit($event->description, 120) }}</p>
                    <div class="training-meta" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="border: 1px solid var(--border-color); padding: 4px 10px; border-radius: var(--radius-sm); font-weight: 700; font-size: 0.75rem; color: var(--text-secondary);">
                            <i class="fas fa-calendar" style="color: var(--primary-400);"></i> {{ $event->start_date->format('d M Y') }}
                        </span>
                        <span style="border: 1px solid var(--border-color); padding: 4px 10px; border-radius: var(--radius-sm); font-weight: 700; font-size: 0.75rem; color: var(--text-secondary);">
                            <i class="fas fa-map-marker-alt" style="color: var(--primary-400);"></i> {{ $event->location }}
                        </span>
                    </div>
                </div>
            </a>
            <div class="training-card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <span style="font-size:0.8125rem;color:var(--text-muted);"><i class="fas fa-users" style="margin-right:0.25rem;"></i> {{ $event->participants_count }}/{{ $event->quota }}</span>
                    <span style="font-size:0.8125rem;color:var(--text-muted);">By {{ $event->creator->name ?? 'N/A' }}</span>
                </div>
                
                @if(auth()->user()->isStudent() && in_array($event->status, ['approved', 'published']))
                    @if(in_array($event->id, $joinedEventIds))
                        <span class="btn btn-sm" style="background: #e2e8f0; color: #64748b; cursor: default;"><i class="fas fa-check"></i> Joined</span>
                    @else
                        <form method="POST" action="{{ route('participants.store', $event) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" {{ $event->isFull() ? 'disabled' : '' }}>
                                <i class="fas fa-sign-in-alt"></i> {{ $event->isFull() ? 'Full' : 'Join' }}
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1;">
            <i class="fas fa-chalkboard-teacher"></i>
            <h3>No events found</h3>
            <p>Check back later for new event opportunities</p>
        </div>
        @endforelse
    </div>

    <div class="pagination-wrapper" style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        {{ $events->withQueryString()->links() }}
    </div>
    </div>
</div>
@endsection
