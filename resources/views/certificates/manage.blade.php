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
                    <td style="font-weight:600;color:var(--text-primary);">{{ $cert->user->name }}</td>
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
    <div class="card-body">
        <div class="action-group mt-3" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
            <a href="{{ route('certificates.design', $event) }}" class="btn btn-outline">
                <i class="fas fa-palette"></i> Design Certificate
            </a>
            <form method="POST" action="{{ route('certificates.activate', $event) }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-primary" onclick="return confirm('This will generate certificates for all accepted participants. Continue?')">
                    <i class="fas fa-certificate"></i> Activate Certificates
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
