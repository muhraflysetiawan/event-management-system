@extends('layouts.app')
@section('title', 'Event Approvals')

@section('content')
@if($events->isEmpty())
    <div class="empty-state card" style="padding: 4rem 2rem; text-align: center;">
        <div style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h4 style="color: var(--text-primary); margin-bottom: 0.5rem;">All caught up!</h4>
        <p style="color: var(--text-muted);">There are no events currently waiting for your approval.</p>
    </div>
@else
    <div class="training-grid">
        @foreach($events as $event)
        <div class="training-card" style="cursor: pointer;" onclick="window.location='{{ route('events.show', $event) }}'">
            <div class="training-card-header">
                <span class="badge-status badge-pending_approval">Pending Approval</span>
                <span style="font-size: 0.75rem; color: rgba(255,255,255,0.8);">
                    {{ count($event->approved_by_roles ?? []) }} / {{ count($event->required_approval_roles ?? []) }} Approved
                </span>
            </div>
            <div class="training-card-body">
                <h3 class="training-title" style="margin-bottom: 0.5rem;">{{ $event->title }}</h3>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                    <i class="fas fa-user-edit"></i> Organizer: {{ $event->creator->name ?? 'N/A' }}
                </div>
                
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                    @foreach($event->required_approval_roles as $role)
                        @php
                            $isApproved = in_array($role, $event->approved_by_roles ?? []);
                        @endphp
                        <span class="badge-status" style="background: {{ $isApproved ? 'rgba(16, 185, 129, 0.1)' : 'rgba(0,0,0,0.05)' }}; color: {{ $isApproved ? '#059669' : '#666' }}; font-size: 0.7rem; display: flex; align-items: center; gap: 4px;">
                            <i class="fas {{ $isApproved ? 'fa-check-circle' : 'fa-circle' }}"></i>
                            {{ strtoupper(str_replace('head_', '', $role)) }}
                        </span>
                    @endforeach
                </div>
            </div>
            <div class="training-card-footer" onclick="event.stopPropagation()">
                <div style="display: flex; gap: 0.75rem; width: 100%;">
                    <form action="{{ route('events.approve', $event) }}" method="POST" style="flex: 1;">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('events.reject', $event) }}" method="POST" style="flex: 1;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; background: #9ca3af; color: white; border: none;">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="pagination-wrapper" style="margin-top: 2rem;">
        {{ $events->links() }}
    </div>
@endif
@endsection
