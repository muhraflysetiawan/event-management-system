@extends('layouts.app')
@section('title', 'Log Details')

@section('content')
<div class="section-header">
    <h3 class="section-title">Log Details #{{ $log->id }}</h3>
    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
    <div style="display:grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <div>
            <h4 class="form-label">Info</h4>
            <table class="table" style="border:none;">
                <tr><td style="border:none;padding-left:0;"><strong>User:</strong></td><td style="border:none;">{{ $log->user->name ?? 'System' }}</td></tr>
                <tr><td style="border:none;padding-left:0;"><strong>Action:</strong></td><td style="border:none;">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td></tr>
                <tr><td style="border:none;padding-left:0;"><strong>Entity:</strong></td><td style="border:none;">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td></tr>
                <tr><td style="border:none;padding-left:0;"><strong>Timestamp:</strong></td><td style="border:none;">{{ $log->created_at->format('d M Y, H:i:s') }}</td></tr>
                <tr><td style="border:none;padding-left:0;"><strong>IP Address:</strong></td><td style="border:none;">{{ $log->ip_address }}</td></tr>
            </table>
            
            <h4 class="form-label" style="margin-top:1.5rem;">User Agent</h4>
            <p style="font-size:0.8125rem;color:var(--text-muted);word-break:break-all;">{{ $log->user_agent }}</p>
        </div>
        
        <div>
            <h4 class="form-label">Changes</h4>
            <div style="background:var(--bg-input); padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 0.8125rem; overflow-x: auto;">
                @if($log->old_values)
                    <p style="color:#ef4444;margin-bottom:0.5rem;">- OLD VALUES:</p>
                    <pre>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                @endif
                
                @if($log->new_values)
                    <p style="color:#10b981;margin:1rem 0 0.5rem 0;">+ NEW VALUES:</p>
                    <pre>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                @endif
                
                @if(!$log->old_values && !$log->new_values)
                    <p class="text-muted">No value changes recorded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
