@extends('layouts.app')
@section('title', 'My Certificates')

@section('content')
@if($certificates->count())
<div class="event-grid">
    @foreach($certificates as $cert)
    <div class="card">
        <div class="card-body" style="text-align:center;padding:2rem;">
            <div style="width:60px;height:60px;background:var(--gradient-primary);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                <i class="fas fa-award" style="font-size:1.5rem;color:white;"></i>
            </div>
            <h3 style="font-weight:700;font-size:1.0625rem;margin-bottom:0.5rem;">{{ $cert->event->title }}</h3>
            <p style="color:var(--text-muted);font-size:0.8125rem;margin-bottom:0.375rem;">Certificate #{{ $cert->certificate_number }}</p>
            <span class="badge-status badge-{{ $cert->status }}">{{ ucfirst($cert->status) }}</span>

            @if($cert->status === 'available')
            <div style="margin-top:1.25rem;">
                <a href="{{ route('certificates.download', $cert) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
<div class="pagination-wrapper">{{ $certificates->links() }}</div>
@else
<div class="card">
    <div class="empty-state" style="padding:3rem;">
        <i class="fas fa-award"></i>
        <h3>No certificates yet</h3>
        <p>Complete a event to earn your certificate</p>
    </div>
</div>
@endif
@endsection
