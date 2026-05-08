@extends('layouts.app')
@section('title', 'Manage Certificates — ' . $event->title)

@section('content')

{{-- ─── Status Banner ─── --}}
@if(!$event->certificate_template || !$event->lecturer_id || !$event->organizer_signature)
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    <span>Certificate design is not configured yet. <a href="{{ route('certificates.design', $event) }}" style="text-decoration: underline; color: inherit; font-weight: 700;">Design it now</a> before activating.</span>
</div>
@else
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i>
    Certificate design is ready. You can activate certificates for all accepted participants.
</div>
@endif

{{-- ─── Main Card ─── --}}
<div class="card" x-data="{ open: false }">
    <div class="card-header">
        <div>
            <h3 class="card-title">{{ $event->title }}</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                <strong>{{ $acceptedParticipants->count() }}</strong> accepted &nbsp;•&nbsp;
                <strong>{{ $certificates->count() }}</strong> certificates issued
            </p>
        </div>
        <div class="action-group">
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('certificates.design', $event) }}" class="btn btn-outline btn-sm">
                <i class="fas fa-palette"></i> Design
            </a>
            <button type="button" class="btn btn-primary btn-sm" @click="open = true">
                <i class="fas fa-certificate"></i> Activate
            </button>
        </div>
    </div>

    <div class="table-container" style="margin: 0; border: none;">
        <table class="table">
            <thead>
                <tr>
                    <th>Participant</th>
                    <th>Email</th>
                    <th>Certificate #</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($certificates as $cert)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $cert->user->name }}</td>
                    <td style="color: var(--text-secondary);">{{ $cert->user->email }}</td>
                    <td>
                        <code style="background: var(--bg-input); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; color: #980517;">
                            {{ $cert->certificate_number }}
                        </code>
                    </td>
                    <td>
                        @if($cert->type !== 'participation')
                            <span style="font-size: 0.7rem; font-weight: 700; color: #92400e; background: #fef3c7; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; white-space: nowrap;">
                                {{ $cert->achievement_title ?: str_replace('_', ' ', $cert->type) }}
                            </span>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.85rem;">Participation</span>
                        @endif
                    </td>
                    <td><span class="badge-status badge-{{ $cert->status }}">{{ ucfirst($cert->status) }}</span></td>
                    <td>
                        <a href="{{ route('certificates.download', $cert) }}" class="btn btn-sm btn-outline" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 2.5rem; color: var(--text-muted);">
                        <i class="fas fa-award" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.3;"></i>
                        No certificates issued yet. Click <strong>Activate</strong> to generate.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ─── Activation Modal ─── --}}
    <div x-show="open" x-cloak class="modal-overlay">
        <div class="modal-card" @click.away="open = false">
            <div style="font-size: 3rem; color: #980517; margin-bottom: 1rem;">
                <i class="fas fa-award"></i>
            </div>
            <h3 style="font-size: 1.375rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text-primary);">Ready to Activate?</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.75rem; line-height: 1.6;">
                This will issue certificates to all <strong>{{ $acceptedParticipants->count() }}</strong> accepted participants. This action cannot be undone.
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <button type="button" class="btn btn-secondary" @click="open = false">Cancel</button>
                <form method="POST" action="{{ route('certificates.activate', $event) }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Yes, Activate
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
