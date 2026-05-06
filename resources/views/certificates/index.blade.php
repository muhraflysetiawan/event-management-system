@extends('layouts.app')
@section('title', 'My Certificates')

@section('content')
@if($certificates->count())
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:1.5rem;">
    @foreach($certificates as $cert)
    <div class="card" style="padding:0; overflow:hidden; border:1px solid var(--border-color); transition:transform 0.2s; background:white;">
        <div style="height:160px; background:linear-gradient(135deg, #980517, #473f3d); position:relative; display:flex; align-items:center; justify-content:center; overflow:hidden;">
             <!-- Decorative elements -->
            <div style="position:absolute; top:-20%; left:-10%; width:100px; height:100px; background:rgba(255,255,255,0.1); border-radius:50%;"></div>
            <div style="position:absolute; bottom:-10%; right:-5%; width:150px; height:150px; background:rgba(255,255,255,0.05); border-radius:50%;"></div>
            
            <div style="width:64px; height:64px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 8px 16px rgba(0,0,0,0.2); z-index:1;">
                <i class="fas fa-award" style="font-size:1.75rem; color:#980517;"></i>
            </div>
        </div>
        <div style="padding:1.5rem; text-align:center;">
            <h3 style="font-weight:800; font-size:1.125rem; margin-bottom:0.5rem; color:var(--text-primary);">{{ $cert->event->title }}</h3>
            <div style="display:flex; flex-direction:column; gap:0.25rem; margin-bottom:1.25rem;">
                @if($cert->type !== 'participation')
                    <div style="margin-bottom: 0.5rem;">
                        <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                            {{ $cert->achievement_title ?: str_replace('_', ' ', $cert->type) }}
                        </span>
                    </div>
                @endif
                <span style="color:var(--text-muted); font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:1px;">Certificate #{{ $cert->certificate_number }}</span>
                <span style="color:var(--text-muted); font-size:0.8125rem;">Earned on {{ $cert->created_at->format('M d, Y') }}</span>
            </div>

            @if($cert->status === 'available')
                <a href="{{ route('certificates.download', $cert) }}" class="btn btn-primary btn-block" style="border-radius:var(--radius-md);">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            @else
                <button class="btn btn-secondary btn-block" disabled style="opacity:0.6; cursor:not-allowed;">
                    <i class="fas fa-clock"></i> Processing...
                </button>
            @endif
        </div>
    </div>
    @endforeach
</div>
<div class="pagination-wrapper" style="margin-top:2rem;">{{ $certificates->links() }}</div>
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
