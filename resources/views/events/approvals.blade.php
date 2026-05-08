@extends('layouts.app')
@section('title', 'Event Approvals')

@section('content')

@if($events->isEmpty())
<div class="card" style="text-align: center; padding: 4rem 2rem;">
    <div style="font-size: 3.5rem; color: #059669; margin-bottom: 1rem; opacity: 0.8;">
        <i class="fas fa-check-circle"></i>
    </div>
    <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">All caught up!</h4>
    <p style="color: var(--text-muted);">There are no events currently waiting for your approval.</p>
</div>
@else

<div class="training-grid">
    @foreach($events as $event)
    <div class="training-card" style="cursor: pointer;" onclick="window.location='{{ route('events.show', $event) }}'">
        {{-- Header --}}
        <div class="training-card-header" style="background: linear-gradient(135deg, #980517, #473f3d); border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 12px; color: #fff;">
                    Pending Approval
                </span>
                <span style="font-size: 0.75rem; color: rgba(255,255,255,0.85);">
                    {{ count($event->approved_by_roles ?? []) }} / {{ count($event->required_approval_roles ?? []) }} approved
                </span>
            </div>
        </div>

        {{-- Body --}}
        <div class="training-card-body">
            <h3 class="training-title">{{ $event->title }}</h3>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="fas fa-user-edit" style="color: #980517;"></i>
                {{ $event->creator->name ?? 'N/A' }}
            </div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="fas fa-calendar-alt" style="color: #980517;"></i>
                {{ $event->start_date->format('d M Y') }}
            </div>

            {{-- Role approval chips --}}
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                @foreach($event->required_approval_roles as $role)
                    @php $isApproved = in_array($role, $event->approved_by_roles ?? []); @endphp
                    <span style="font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; display: inline-flex; align-items: center; gap: 4px;
                        background: {{ $isApproved ? 'rgba(16,185,129,0.1)' : 'rgba(107,114,128,0.1)' }};
                        color: {{ $isApproved ? '#059669' : '#6b7280' }};
                        border: 1px solid {{ $isApproved ? 'rgba(16,185,129,0.3)' : 'rgba(107,114,128,0.2)' }};">
                        <i class="fas {{ $isApproved ? 'fa-check-circle' : 'fa-circle' }}" style="color: inherit; font-size: 0.65rem;"></i>
                        {{ strtoupper(str_replace(['head_', '_'], ['', ' '], $role)) }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="training-card-footer" onclick="event.stopPropagation()">
            <div style="display: flex; gap: 0.5rem; width: 100%;">
                <form action="{{ route('events.approve', $event) }}" method="POST" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </form>
                <form action="{{ route('events.reject', $event) }}" method="POST" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="pagination-wrapper">{{ $events->links() }}</div>
@endif

@endsection
