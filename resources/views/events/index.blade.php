@extends('layouts.app')
@section('title', 'Events')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="filter-bar" style="display:flex; flex-direction:column; gap:0.75rem;">
            <form method="GET" action="{{ route('events.index') }}" style="display:flex; gap:0.5rem; flex-wrap:wrap; width:100%; align-items:center;">
                <div style="display:flex; gap:0.5rem; flex:1; min-width:220px;">
                    <input type="text" name="search" class="form-input" placeholder="Search events..." value="{{ request('search') }}" style="flex:1;">
                    <select name="status" class="form-input" style="width:110px; font-size:0.85rem;" onchange="this.form.submit()">
                        <option value="">Status</option>
                        @foreach(['draft','pending_approval','approved','published','ongoing','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:0.5rem; align-items:center; width:100%; justify-content: space-between; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary btn-sm" style="flex:1; min-width:100px;">Search</button>
                    
                    @if(auth()->user()->isAdmin() || auth()->user()->isCommittee() || auth()->user()->isLecturer() || auth()->user()->isStaff() || auth()->user()->isExternal())
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm" style="flex:1; min-width:100px;">Create</a>
                    @endif
                </div>
            </form>
        </div>

    <div class="events-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        @forelse($events as $event)
        <div class="event-card-premium" onclick="window.location='{{ route('events.show', $event) }}'" style="cursor: pointer;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span class="badge-status">{{ ucfirst($event->status) }}</span>
                <div style="font-size: 0.75rem;">
                    <i class="fas fa-users" style="margin-right: 0.25rem;"></i> {{ $event->participants->count() }}/{{ $event->quota }}
                </div>
            </div>

            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #FFFFFF !important;">
                {{ $event->title }}
            </h3>

            <p style="font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; color: #FFFFFF !important;">
                {{ $event->description }}
            </p>

            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <span style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF !important;">
                    <i class="fas fa-calendar-alt" style="margin-right: 0.25rem;"></i> {{ $event->start_date->format('d M Y') }}
                </span>
                <span style="background: rgba(255, 255, 255, 0.1); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2); color: #FFFFFF !important;">
                    <i class="fas fa-map-marker-alt" style="margin-right: 0.25rem;"></i> {{ $event->location }}
                </span>
            </div>

            <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; margin-top: auto;" onclick="event.stopPropagation()">
                <div style="font-size: 0.75rem; opacity: 0.8;">
                    By {{ $event->creator->name ?? 'Admin' }}
                </div>
                
                @if($userParticipants->has($event->id))
                    <span class="badge-status" style="background: rgba(255,255,255,0.1) !important; color: white !important;">
                        <i class="fas fa-check"></i> Registered
                    </span>
                @elseif($event->created_by === auth()->id() && $event->isFullyApproved() && $event->status === 'pending_approval')
                    <form method="POST" action="{{ route('events.publish', $event) }}">
                        @csrf
                        <button type="submit" class="btn-white">
                            <i class="fas fa-paper-plane"></i> Post
                        </button>
                    </form>
                @elseif(in_array(auth()->user()->role->slug ?? '', $event->target_audience ?? []) && in_array($event->status, ['published', 'ongoing']))
                    <form method="POST" action="{{ route('participants.store', $event) }}">
                        @csrf
                        <button type="submit" class="btn-white" {{ $event->isFull() ? 'disabled' : '' }}>
                            <i class="fas fa-sign-in-alt"></i> Join
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem;">
            <i class="fas fa-calendar-times" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 1.5rem;"></i>
            <h3>No events found</h3>
            <p style="color: var(--text-muted);">Try adjusting your search filters</p>
        </div>
        @endforelse
    </div>

    <div class="pagination-wrapper" style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
        {{ $events->withQueryString()->links() }}
    </div>
    </div>
</div>
@endsection
