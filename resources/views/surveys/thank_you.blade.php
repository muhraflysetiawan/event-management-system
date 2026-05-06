@extends('layouts.app')
@section('title', 'Thank You!')

@section('content')
<div class="card" style="max-width: 600px; margin: 5rem auto; text-align: center; padding: 3rem;">
    <div style="font-size: 5rem; color: #10b981; margin-bottom: 2rem;">
        <i class="fas fa-check-circle"></i>
    </div>
    <h2 class="mb-3">Thank You for Your Feedback!</h2>
    <p class="mb-5 text-muted" style="font-size: 1.1rem;">Your survey has been submitted successfully. Your certificate download should start automatically in a few seconds.</p>
    
    <div id="download-status" class="mb-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2" style="font-size: 0.9rem;">Starting download...</p>
    </div>

    <div class="action-group" style="justify-content: center; gap: 1rem;">
        <a id="download-link" href="{{ route('certificates.download', $certificate) }}" class="btn btn-primary">
            <i class="fas fa-download"></i> Click here if download doesn't start
        </a>
        <a href="{{ route('certificates.index') }}" class="btn btn-secondary">
            Go to My Certificates
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delay download for 2 seconds to let the user read the message
    setTimeout(function() {
        window.location.href = "{{ route('certificates.download', $certificate) }}";
        document.getElementById('download-status').innerHTML = '<p style="color: #10b981;"><i class="fas fa-check"></i> Download started!</p>';
    }, 2000);
});
</script>
@endsection
