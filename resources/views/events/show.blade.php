@extends('layouts.app')
@section('title', $event->title)

@section('content')

<div class="card mb-3">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <h3 class="card-title" style="margin: 0;">{{ $event->title }}</h3>
            @php
                $user = auth()->user();
                $hasAlreadyApproved = in_array($user->role->slug ?? '', $event->approved_by_roles ?? []);
            @endphp
            @if($hasAlreadyApproved && $event->status === 'pending_approval')
                <span class="badge-status" style="font-size: 0.75rem; background: #980517; padding: 4px 8px; border-radius: 4px;">
                    <i class="fas fa-check-double"></i> {{ count($event->approved_by_roles ?? []) }} / {{ count($event->required_approval_roles ?? []) }} Approved
                </span>
            @endif
        </div>
        <span class="badge-status badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
    </div>
    <div class="card-body">
        <div class="detail-grid mb-4">
            <div class="detail-item">
                <div class="detail-label">Event ID</div>
                <div class="detail-value" style="font-weight: 700; color: var(--primary-400);">{{ $event->generated_id }}</div>
            </div>
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
            <div style="color:var(--text-secondary);line-height:1.8;white-space:pre-line; overflow-wrap: break-word;">{{ $event->description }}</div>
        </div>

        @if((auth()->user()->isAdmin() || auth()->user()->isCommittee() || auth()->user()->isHeadDepartment() || auth()->user()->isACOO() || $event->created_by === auth()->id()) && $event->project_brief)
        <div style="margin-bottom:1.5rem; background: var(--bg-input); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.75rem;">
                <div class="detail-label" style="color: var(--primary-400); margin-bottom: 0;"><i class="fas fa-file-signature"></i> Project Brief</div>
                <a href="{{ route('events.projectBriefPdf', $event) }}" class="btn btn-sm btn-outline" target="_blank" style="width: 100%; text-align: center;"><i class="fas fa-file-pdf"></i> View / Download PDF</a>
            </div>
            <div style="color:var(--text-secondary);line-height:1.8; overflow-x: auto;" class="rich-text-container">
                {!! $event->project_brief !!}
            </div>
        </div>
        @endif

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
        <div class="action-group" style="display: flex; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap; width: 100%;">
            <a href="{{ route('events.index') }}" class="btn btn-secondary" style="flex: 1; min-width: 100px; justify-content: center;"><i class="fas fa-arrow-left"></i> Back</a>

            @php
                $user = auth()->user();
                $userRoleSlug = $user->role->slug ?? '';
                $isCreator = $event->created_by === $user->id;
                $isAdmin = $user->isAdmin();
                $isFullyApproved = $event->isFullyApproved();
                $isPublished = in_array($event->status, ['published', 'ongoing', 'completed']);
                $isDraft = $event->status === 'draft';
                $isPending = $event->status === 'pending_approval';
                
                $isRequiredToApprove = in_array($userRoleSlug, $event->required_approval_roles ?? []);
                $hasAlreadyApproved = in_array($userRoleSlug, $event->approved_by_roles ?? []);
                
                // Permission to edit: Admin OR (Creator AND isDraft)
                $canEdit = $isAdmin || ($isCreator && $isDraft);
            @endphp

            @if($canEdit)
                <a href="{{ route('events.edit', $event) }}" class="btn btn-outline"><i class="fas fa-edit"></i> Edit</a>
            @endif

            {{-- Creator/Admin Actions before/during approval --}}
            @if(($isCreator || $isAdmin) && $isPending)
                <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan event ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="background: #ef4444; color: white; border: none;"><i class="fas fa-trash"></i> Batalkan Event</button>
                </form>

                @if($isFullyApproved)
                    <form method="POST" action="{{ route('events.publish', $event) }}">
                        @csrf
                        <button type="submit" class="btn btn-success" style="background: #059669; color: white; border: none;">
                            <i class="fas fa-paper-plane"></i> Posting
                        </button>
                    </form>
                @endif
            @endif

            {{-- Management Actions (Only after published or if Admin) --}}
            @if($isAdmin || ($isCreator && $isPublished))
                <a href="{{ route('attendance.generate', $event) }}" class="btn btn-outline"><i class="fas fa-qrcode"></i> Attendance</a>
                <a href="{{ route('reports.create', $event) }}" class="btn btn-outline"><i class="fas fa-file-alt"></i> Create Report</a>
                <a href="{{ route('certificates.manage', $event) }}" class="btn btn-outline"><i class="fas fa-award"></i> Certificates</a>
            @endif

            {{-- Head Approval Actions --}}
            @if($isRequiredToApprove && !$hasAlreadyApproved && $isPending)
                <form method="POST" action="{{ route('events.approve', $event) }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Approve This Event</button>
                </form>
                <form method="POST" action="{{ route('events.reject', $event) }}" style="display:inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="background: #9ca3af; color: white; border: none;"><i class="fas fa-times"></i> Reject</button>
                </form>
            @endif

            {{-- Participant Join Action --}}
            @if(!$isCreator && !$isAdmin && in_array($userRoleSlug, $event->target_audience ?? []) && in_array($event->status, ['published', 'ongoing']))
                @if($userParticipant)
                    <div class="alert" style="margin:0; background: rgba(152, 5, 23, 0.1); border: 1px solid rgba(152, 5, 23, 0.3); color: #980517;">
                        <i class="fas fa-check-circle"></i> Terdaftar (#{{ $userParticipant->registration_number }})
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
        </div>
    </div>
</div>

{{-- Flow Control Button (Between details and participants) --}}
@if(($isAdmin || $isCreator) && in_array($event->status, ['published', 'ongoing']))
<div class="card mb-3" style="background: #980517; border: none;">
    <div class="card-body" style="padding: 1rem; display: flex; justify-content: center;">
        <form method="POST" action="{{ route('events.updateStatus', $event) }}" style="width: 100%;">
            @csrf
            <button type="submit" class="btn-flow-control" style="width: 100%; background: #FFFFFF !important; color: #980517 !important; font-weight: 800 !important; border: none !important; padding: 1rem !important; border-radius: 12px !important; cursor: pointer !important; font-size: 1.1rem !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 0.75rem !important;">
                @if($event->status === 'published')
                    <i class="fas fa-play" style="color: #980517 !important;"></i> START EVENT (Move to Ongoing)
                @elseif($event->status === 'ongoing')
                    <i class="fas fa-check-double" style="color: #980517 !important;"></i> COMPLETE EVENT (Move to Completed)
                @endif
            </button>
        </form>
    </div>
</div>
@endif

@if($isAdmin || ($isCreator && $isPublished))
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
