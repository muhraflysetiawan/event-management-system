@extends('layouts.app')
@section('title', $event->title)

@section('content')
@php
    $user = auth()->user();
    $userRoleSlug = $user->role->slug ?? '';
    $isCreator = $event->created_by === $user->id;
    $isAdmin = $user->isAdmin();
    $isFullyApproved = $event->isFullyApproved();
    $isPublished = in_array($event->status, ['published', 'ongoing', 'completed']);
    $isDraft = $event->status === 'draft';
    $isPending = $event->status === 'pending_approval';
    $hasAlreadyApproved = in_array($userRoleSlug, $event->approved_by_roles ?? []);
    $isRequiredToApprove = in_array($userRoleSlug, $event->required_approval_roles ?? []);
    $canEdit = $isAdmin || ($isCreator && $isDraft);
@endphp

{{-- ─── EVENT DETAILS CARD ─── --}}
<div class="card mb-3">
    <div class="card-header">
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; flex: 1; min-width: 0;">
            <h3 class="card-title" style="margin: 0;">{{ $event->title }}</h3>
            @if(($hasAlreadyApproved || $isCreator) && $isPending)
                <span class="badge-status badge-pending" style="white-space: nowrap;">
                    <i class="fas fa-check-double" style="color: inherit;"></i>
                    {{ count($event->approved_by_roles ?? []) }} / {{ count($event->required_approval_roles ?? []) }} Approved
                </span>
            @endif
        </div>
        <span class="badge-status badge-{{ $event->status }}" style="flex-shrink: 0;">
            {{ ucfirst(str_replace('_', ' ', $event->status)) }}
        </span>
    </div>

    <div class="card-body">

        {{-- ─── Info Grid ─── --}}
        <div class="detail-grid mb-4">
            <div class="detail-item">
                <div class="detail-label">Event ID</div>
                <div class="detail-value" style="color: #980517; font-size: 0.875rem; word-break: break-all;">{{ $event->generated_id }}</div>
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
                <div class="detail-value" style="color: {{ $event->availableSlots() > 0 ? '#059669' : '#dc2626' }};">
                    {{ $event->availableSlots() > 0 ? $event->availableSlots() . ' remaining' : 'Full' }}
                </div>
            </div>
        </div>

        {{-- ─── Approval Progress Visualization (Bar per role) ─── --}}
        @if(($isAdmin || $isCreator) && $isPending)
        <div style="margin-bottom: 2rem;">
            <div class="detail-label" style="margin-bottom: 0.75rem; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-tasks" style="color: #980517; margin-right: 0.4rem;"></i> Approval Progress</span>
                <span style="font-weight: 700; color: #980517;">{{ count($event->approved_by_roles ?? []) }} / {{ count($event->required_approval_roles ?? []) }} Approved</span>
            </div>
            <div style="display: flex; gap: 0.6rem; width: 100%; height: 10px; margin-bottom: 0.75rem;">
                @foreach($event->required_approval_roles ?? [] as $roleSlug)
                    @php
                        $roleApproved = in_array($roleSlug, $event->approved_by_roles ?? []);
                    @endphp
                    <div style="flex: 1; height: 100%; background: {{ $roleApproved ? '#059669' : '#e5e7eb' }}; border-radius: 6px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);" 
                         title="{{ ucfirst(str_replace('_', ' ', $roleSlug)) }}: {{ $roleApproved ? 'Approved' : 'Pending' }}">
                    </div>
                @endforeach
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                @foreach($event->required_approval_roles ?? [] as $roleSlug)
                    @php
                        $roleApproved = in_array($roleSlug, $event->approved_by_roles ?? []);
                        $roleName = ucfirst(str_replace('_', ' ', $roleSlug));
                    @endphp
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; color: {{ $roleApproved ? '#059669' : 'var(--text-muted)' }};">
                        <i class="fas {{ $roleApproved ? 'fa-check-circle' : 'fa-circle' }}" style="color: inherit; font-size: 0.65rem;"></i>
                        {{ $roleName }}
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ─── Description ─── --}}
        <div style="margin-bottom: 2.5rem;">
            <div class="detail-label" style="margin-bottom: 0.75rem;">Description</div>
            <div style="color: var(--text-secondary); line-height: 1.8; white-space: pre-line; overflow-wrap: break-word;">{{ $event->description }}</div>
        </div>

        {{-- ─── Project Brief (management only) ─── --}}
        @if((auth()->user()->isAdmin() || auth()->user()->isCommittee() || auth()->user()->isHeadDepartment() || auth()->user()->isACOO() || $isCreator) && ($event->project_brief || $event->project_brief_pdf))
        <div style="margin-bottom: 2.5rem; background: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <div class="detail-label" style="margin-bottom: 0;">
                    <i class="fas fa-file-signature" style="color: #980517; margin-right: 0.4rem;"></i> Project Brief
                </div>
                <a href="{{ route('events.projectBriefPdf', $event) }}" class="btn btn-sm btn-outline" target="_blank">
                    <i class="fas fa-file-pdf"></i> View / Download PDF
                </a>
            </div>
            @if($event->project_brief_type === 'text')
            <div style="color: var(--text-secondary); line-height: 1.8; overflow-x: auto;" class="rich-text-container">
                {!! $event->project_brief !!}
            </div>
            @endif
        </div>
        @endif

        {{-- ─── Materials ─── --}}
        @if($event->materials->count() > 0)
        <div style="margin-bottom: 2.5rem;">
            <div class="detail-label" style="margin-bottom: 1rem;">Materials</div>
            <div class="detail-grid" style="gap: 1.5rem;">
                @foreach($event->materials as $material)
                <div class="detail-item" style="border-left: 3px solid #980517; padding-left: 1rem;">
                    <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem;">{{ $material->title }}</div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">{{ $material->description }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ─── Action Buttons ─── --}}
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; padding-top: 1rem; border-top: 1px solid var(--border-color); margin-top: 0.5rem; align-items: center;">
            <a href="{{ route('events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>

            @if($canEdit)
                <a href="{{ route('events.edit', $event) }}" class="btn btn-outline">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif

            {{-- Creator/Admin: cancel or post while pending --}}
            @if(($isCreator || $isAdmin) && $isPending)
                @if($isFullyApproved)
                    <form method="POST" action="{{ route('events.publish', $event) }}" id="publish-section">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Post Event
                        </button>
                    </form>
                @endif
                <form action="{{ route('events.destroy', $event) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan event ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Batalkan
                    </button>
                </form>
            @endif

            {{-- Management tools (published/ongoing/completed) --}}
            @if($isAdmin || ($isCreator && $isPublished) || ($user->isExternal() && $isPublished))
                <a href="{{ route('attendance.generate', $event) }}" class="btn btn-outline">
                    <i class="fas fa-qrcode"></i> Attendance
                </a>
                <a href="{{ route('reports.create', $event) }}" class="btn btn-outline">
                    <i class="fas fa-file-alt"></i> Report
                </a>
                <a href="{{ route('certificates.manage', $event) }}" class="btn btn-outline">
                    <i class="fas fa-award"></i> Certificates
                </a>
                <a href="{{ route('surveys.manage', $event) }}" class="btn btn-outline">
                    <i class="fas fa-poll"></i> Survey
                </a>
                <a href="{{ route('surveys.requirements.manage', $event) }}" class="btn btn-outline">
                    <i class="fas fa-tasks"></i> Requirements
                </a>
            @endif

            {{-- Head Approval Buttons --}}
            @if($isRequiredToApprove && !$hasAlreadyApproved && $isPending)
                <form method="POST" action="{{ route('events.approve', $event) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Approve
                    </button>
                </form>
                <form method="POST" action="{{ route('events.reject', $event) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </form>
            @endif

            {{-- Participant Join Action --}}
            @if(!$isCreator && !$isAdmin && in_array($userRoleSlug, $event->target_audience ?? []) && in_array($event->status, ['published', 'ongoing']))
                @if($userParticipant)
                    <div class="badge-status badge-accepted" style="padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-size: 0.875rem; border: 1px solid rgba(16,185,129,0.3);">
                        <i class="fas fa-check-circle" style="color: inherit;"></i>
                        Terdaftar #{{ $userParticipant->registration_number }}
                    </div>
                @else
                    <form method="POST" action="{{ route('participants.store', $event) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary" {{ $event->isFull() ? 'disabled' : '' }}>
                            <i class="fas fa-sign-in-alt"></i> {{ $event->isFull() ? 'Event Full' : 'Join Event' }}
                        </button>
                    </form>
                @endif
            @endif
        </div>

    </div>{{-- /card-body --}}
</div>{{-- /card --}}

{{-- ─── Event Status Flow Control ─── --}}
@if(($isAdmin || $isCreator) && in_array($event->status, ['published', 'ongoing']))
<div class="card mb-3" style="background: linear-gradient(135deg, #980517, #473f3d); border: none; padding: 0;">
    <div style="padding: 1.25rem;">
        <form method="POST" action="{{ route('events.updateStatus', $event) }}">
            @csrf
            <button type="submit" style="width: 100%; background: rgba(255,255,255,0.12); color: #FFFFFF; font-weight: 800; border: 2px solid rgba(255,255,255,0.4); padding: 0.9rem 1.5rem; border-radius: var(--radius-md); cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; letter-spacing: 0.03em;">
                @if($event->status === 'published')
                    <i class="fas fa-play" style="color: #FFFFFF;"></i> START EVENT — Move to Ongoing
                @elseif($event->status === 'ongoing')
                    <i class="fas fa-flag-checkered" style="color: #FFFFFF;"></i> COMPLETE EVENT — Mark as Completed
                @endif
            </button>
        </form>
    </div>
</div>
@endif

{{-- ─── Participants Table ─── --}}
@if($isAdmin || ($isCreator && $isPublished) || ($user->isExternal() && $isPublished))
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Participants <span style="color: var(--text-muted); font-size: 0.875rem; font-weight: 500;">({{ $event->participants->count() }})</span></h3>
    </div>
    <div class="table-container" style="margin: 0; border: none;">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Reg #</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($event->participants as $reg)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $reg->user->name }}</td>
                    <td style="color: var(--text-secondary);">{{ $reg->user->email }}</td>
                    <td><code style="background: var(--bg-input); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; color: #980517;">{{ $reg->registration_number }}</code></td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                    <td>
                        @if($reg->status === 'pending')
                        <div class="action-group">
                            <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button class="btn btn-sm btn-primary" title="Accept"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('participants.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm btn-danger" title="Reject"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @else
                        <span style="color: var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                        <i class="fas fa-users" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                        No participants yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
