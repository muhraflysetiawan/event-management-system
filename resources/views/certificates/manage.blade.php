@extends('layouts.app')
@section('title', 'Manage Certificates')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        @if(!$event->certificate_template || !$event->lecturer_id || !$event->organizer_signature)
            <div class="alert alert-warning" style="margin-bottom:1.5rem;">
                <i class="fas fa-exclamation-triangle"></i> Certificate design is not configured yet. Please design the certificate before activating.
            </div>
        @else
            <div class="alert alert-info" style="margin-bottom:1.5rem;">
                <i class="fas fa-check-circle"></i> Certificate design is ready. You can now activate certificates.
            </div>
        @endif

        <p style="color:var(--text-muted);margin-bottom:1rem;">
            <strong>{{ $acceptedParticipants->count() }}</strong> accepted participants •
            <strong>{{ $certificates->count() }}</strong> certificates issued
        </p>
    </div>
    <div class="table-container">
        <table class="table">
            <thead><tr><th>Participant</th><th>Email</th><th>Certificate #</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($certificates as $cert)
                <tr>
                    <td style="font-weight:600;color:var(--text-primary);">
                        {{ $cert->user->name }}
                        @if($cert->type !== 'participation')
                            <div style="font-size: 0.7rem; color: #92400e; background: #fef3c7; display: inline-block; padding: 0.1rem 0.4rem; border-radius: 4px; margin-left: 0.5rem; text-transform: uppercase;">
                                {{ $cert->achievement_title ?: str_replace('_', ' ', $cert->type) }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $cert->user->email }}</td>
                    <td><code style="color:var(--primary-400);">{{ $cert->certificate_number }}</code></td>
                    <td><span class="badge-status badge-{{ $cert->status }}">{{ ucfirst($cert->status) }}</span></td>
                    <td>
                        <a href="{{ route('certificates.download', $cert) }}" class="btn btn-sm btn-outline"><i class="fas fa-download"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;color:var(--text-muted);">No certificates issued yet. Click "Activate Certificates" to generate.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body" x-data="{ open: false }">
        <div class="action-group mt-3" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            <a href="{{ route('certificates.design', $event) }}" class="btn btn-outline">
                <i class="fas fa-palette"></i> Design Certificate
            </a>
            
            <button type="button" class="btn btn-primary" @click="open = true" style="position:relative; z-index:50; cursor:pointer;">
                <i class="fas fa-certificate"></i> Activate Certificates
            </button>

            <!-- Custom Modal -->
            <div x-show="open" x-cloak class="modal-overlay" style="display: flex;">
                <div class="modal-card" @click.away="open = false">
                    <div style="font-size: 3.5rem; color: #980517; margin-bottom: 1.5rem;">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 1rem; color: #1f2937;">Ready to Activate?</h3>
                    <p style="color: #6b7280; margin-bottom: 2rem; line-height: 1.6;">This will finalize and issue certificates to all participants who have completed their attendance requirements.</p>
                    
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button type="button" class="btn btn-secondary" @click="open = false">Cancel</button>
                        <form method="POST" action="{{ route('certificates.activate', $event) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="background: #980517 !important; color: white !important; border: none !important; padding: 0.75rem 2rem !important; border-radius: 10px !important;">
                                Yes, Activate Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
