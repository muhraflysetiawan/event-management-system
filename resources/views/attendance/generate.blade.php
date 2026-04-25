@extends('layouts.app')
@section('title', 'Generate QR Code')

@section('content')

<div class="card">
    <div class="card-body">
        @if(session('qr_generated'))
            <div class="qr-display">
                <h3 style="font-weight:700;margin-bottom:0.5rem;">Scan this QR Code</h3>
                <p style="color:var(--text-muted);font-size:0.875rem;">Participants scan this code to check in</p>

                <div style="background:white;display:inline-block;padding:1.5rem;border-radius:var(--radius-lg);margin:1.5rem 0;">
                    {!! QrCode::size(250)->generate(session('qr_url')) !!}
                </div>

                <div class="qr-timer" x-data="qrTimer('{{ session('expires_at') }}')" x-init="startTimer()">
                    <div style="font-size:0.8125rem;color:var(--text-muted);margin-bottom:0.5rem;">Expires in</div>
                    <span x-text="timeLeft" style="font-family:monospace;"></span>
                </div>

                <div style="margin-top:1rem;padding:1rem;background:var(--bg-input);border-radius:var(--radius-sm);border:1px solid var(--border-color);">
                    <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.375rem;">Manual Check-in URL</div>
                    <code style="font-size:0.75rem;color:var(--primary-400);word-break:break-all;">{{ session('qr_url') }}</code>
                <div class="action-group mt-3" style="display: flex; justify-content: flex-end;">
                    <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Back to Event</a>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('attendance.generateQR', $event) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">QR Code Duration (minutes)</label>
                    <input type="number" name="duration" class="form-input" value="60" min="5" max="480" required>
                    <p style="font-size:0.8125rem;color:var(--text-muted);margin-top:0.375rem;">QR code will expire after this duration</p>
                </div>
                <div class="action-group mt-3" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-qrcode"></i> Generate QR Code</button>
                </div>
            </form>

            @if($event->isQrValid())
            <div class="alert alert-info mt-2">
                <i class="fas fa-info-circle"></i>
                An active QR code already exists. It expires at {{ $event->qr_expires_at->format('H:i:s') }}.
                Generating a new one will replace it.
            </div>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('qrTimer', (expiresAt) => ({
        timeLeft: '',
        interval: null,
        startTimer() {
            const expires = new Date(expiresAt);
            this.interval = setInterval(() => {
                const now = new Date();
                const diff = expires - now;
                if (diff <= 0) {
                    this.timeLeft = 'Expired!';
                    clearInterval(this.interval);
                    return;
                }
                const mins = Math.floor(diff / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                this.timeLeft = `${mins}m ${secs}s`;
            }, 1000);
        }
    }));
});
</script>
@endpush
@endsection
