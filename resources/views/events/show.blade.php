@extends('layouts.app')
@section('title', $event->title)

@section('content')

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">{{ $event->title }}</h3>
        <span class="badge-status badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
    </div>
    <div class="card-body">
        <div class="detail-grid mb-3">
            <div class="detail-item">
                <div class="detail-label">Start Date</div>
                <div class="detail-value">{{ $event->start_date->format('d M Y, H:i') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">End Date</div>
                <div class="detail-value">{{ $event->end_date->format('d M Y, H:i') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Location</div>
                <div class="detail-value">{{ $event->location }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Quota</div>
                <div class="detail-value">{{ $event->acceptedParticipants()->count() }} / {{ $event->quota }} slots</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Created By</div>
                <div class="detail-value">{{ $event->creator->name ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Available Slots</div>
                <div class="detail-value" style="color:{{ $event->availableSlots() > 0 ? '#6ee7b7' : '#fca5a5' }};">
                    {{ $event->availableSlots() > 0 ? $event->availableSlots() . ' remaining' : 'Full' }}
                </div>
            </div>
        </div>

        <div style="margin-bottom:1.5rem;">
            <div class="detail-label" style="margin-bottom:0.75rem;">Description</div>
            <div style="color:var(--text-secondary);line-height:1.8;white-space:pre-line;">{{ $event->description }}</div>
        </div>

        @if($event->materials->count() > 0)
        <div style="margin-bottom:1.5rem;">
            <div class="detail-label" style="margin-bottom:0.75rem;">Materials</div>
            <div class="detail-grid">
                @foreach($event->materials as $material)
                <div class="detail-item" style="border-left: 3px solid var(--primary-400); padding-left: 10px;">
                    <div style="font-weight:bold; color:var(--text-primary);">{{ $material->title }}</div>
                    <div style="color:var(--text-secondary); font-size:0.9rem;">{{ $material->description }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="action-group" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            @if(auth()->user()->isStudent() && in_array($event->status, ['approved', 'published']))
                @if($userParticipant)
                    <div class="alert alert-info" style="margin:0;">
                        <i class="fas fa-info-circle"></i>
                        You have joined this event. Registration #{{ $userParticipant->registration_number }}
                    </div>
                @else
                    <form method="POST" action="{{ route('participants.store', $event) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg" {{ $event->isFull() ? 'disabled' : '' }}>
                            <i class="fas fa-sign-in-alt"></i> {{ $event->isFull() ? 'Event Full' : 'Join Event' }}
                        </button>
                    </form>
                @endif
            @endif

            @if(auth()->user()->isAdmin() || (auth()->user()->isCommittee() && $event->created_by === auth()->id()))
                <a href="{{ route('events.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                <a href="{{ route('events.edit', $event) }}" class="btn btn-outline"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('attendance.generate', $event) }}" class="btn btn-outline"><i class="fas fa-qrcode"></i> QR Attendance</a>
                <a href="{{ route('attendance.list', $event) }}" class="btn btn-outline"><i class="fas fa-list-check"></i> Attendance List</a>
                <a href="{{ route('reports.create', $event) }}" class="btn btn-outline"><i class="fas fa-file-alt"></i> Create Report</a>
                <a href="{{ route('certificates.manage', $event) }}" class="btn btn-outline"><i class="fas fa-award"></i> Certificates</a>
@endif

            @if(auth()->user()->isHeadDepartment() && $event->status === 'pending_approval')
                <form method="POST" action="#" style="display:inline-block;">
                    @csrf
                    <!-- This would route to approval controller -->
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Approve Event</button>
                </form>
                <form method="POST" action="#" style="display:inline-block;">
                    @csrf
                    <!-- This would route to approval controller -->
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> Reject</button>
                </form>
            @endif
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin() || auth()->user()->isCommittee())
<!-- Participants Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Participants ({{ $event->participants->count() }})</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Reg #</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($event->participants as $reg)
                <tr>
                    <td style="font-weight:600;color:var(--text-primary);">{{ $reg->user->name }}</td>
                    <td>{{ $reg->user->email }}</td>
                    <td><code style="color:var(--primary-400);">{{ $reg->registration_number }}</code></td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                    <td>
                        @if($reg->status === 'pending')
                        <div class="action-group">
                            <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;color:var(--text-muted);">No participants yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
