@extends('layouts.app')
@section('title', 'Scan QR Code')

@section('content')
<div class="card">
    <div class="card-body" x-data="qrScanner()">
        <p style="color:var(--text-muted);margin-bottom:1.5rem;text-align:center;">
            Point your camera at the QR code displayed by your trainer to check in.
        </p>

        <div x-show="!started" style="text-align:center;padding:2rem;">
            <button @click="startScanner()" class="btn btn-primary btn-lg" style="padding:1rem 2rem;">
                <i class="fas fa-camera" style="margin-right:0.5rem;"></i> Start Scanning
            </button>
        </div>

        <div id="qr-reader" x-show="started" style="width:100%;border-radius:var(--radius-md);overflow:hidden;margin-bottom:1rem;"></div>

        <div x-show="scanning && started" style="text-align:center;color:var(--primary-400);">
            <i class="fas fa-spinner fa-spin" style="margin-right:0.5rem;"></i> Scanning...
        </div>

        <div x-show="result" class="alert" :class="success ? 'alert-success' : 'alert-error'" style="margin-top:1rem;">
            <i :class="success ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
            <span x-text="result"></span>
        </div>

        <div style="text-align:center;margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid var(--border-color);">
            <p style="color:var(--text-muted);font-size:0.875rem;margin-bottom:0.75rem;">Or enter the check-in URL manually:</p>
            <form method="GET" action="{{ route('attendance.checkin.form') }}" style="display:flex;gap:0.5rem;">
                <input type="text" name="token" class="form-input" placeholder="Enter QR token..." required>
                <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i></button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('qrScanner', () => ({
        started: false,
        scanning: false,
        result: '',
        success: false,
        html5QrCode: null,
        init() {
            this.html5QrCode = new Html5Qrcode("qr-reader");
        },
        startScanner() {
            this.started = true;
            this.scanning = true;
            this.html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    this.html5QrCode.stop();
                    this.scanning = false;
                    window.location.href = decodedText;
                },
                (errorMessage) => {}
            ).catch((err) => {
                this.scanning = false;
                this.started = false;
                this.result = 'Camera access denied or not available. Use manual entry below.';
                this.success = false;
            });
        }
    }));
});
</script>
@endpush
@endsection
