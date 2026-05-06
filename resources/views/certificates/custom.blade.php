@extends('layouts.app')
@section('title', 'Issue Special Certificate')

@section('content')
<div class="container-narrow">
    <div class="card card-premium">
        <div class="card-header-premium">
            <div class="header-content">
                <i class="fas fa-award header-icon"></i>
                <div>
                    <h2 class="header-title">Special Certificate</h2>
                    <p class="header-subtitle">Award a special recognition for <strong>{{ $user->name }}</strong></p>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="info-banner">
                <div class="info-icon"><i class="fas fa-info-circle"></i></div>
                <div class="info-text">
                    This certificate will be added to the participant's collection and they will receive a congratulatory notification.
                </div>
            </div>

            <form action="{{ route('certificates.custom.store', $participant) }}" method="POST" class="form-premium">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="type">Recognition Type</label>
                    <div class="select-wrapper">
                        <select name="type" id="type" class="form-input" required>
                            <option value="" disabled selected>Select recognition type...</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down select-icon"></i>
                    </div>
                    <p class="form-help">Choose the specific achievement for this participant.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="achievement_title">Achievement Title (Optional)</label>
                    <input type="text" name="achievement_title" id="achievement_title" class="form-input" placeholder="e.g. Winner of Mobile Dev Competition">
                    <p class="form-help">Specify the exact achievement text to be printed on the certificate.</p>
                </div>

                <div class="participant-preview">
                    <div class="preview-label">Participant Details</div>
                    <div class="preview-card">
                        <div class="participant-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="participant-info">
                            <div class="participant-name">{{ $user->name }}</div>
                            <div class="participant-email">{{ $user->email }}</div>
                            <div class="event-tag">{{ $event->title }}</div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('participants.index', ['event_id' => $event->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary btn-glow">
                        <i class="fas fa-paper-plane"></i> Issue Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .container-narrow {
        max-width: 600px;
        margin: 2rem auto;
    }
    
    .card-premium {
        background: var(--card-bg, #fff);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-header-premium {
        background: linear-gradient(135deg, var(--primary-600, #4f46e5), var(--primary-800, #3730a3));
        padding: 2rem;
        color: white;
    }
    
    .header-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .header-icon {
        font-size: 2.5rem;
        background: rgba(255,255,255,0.2);
        padding: 1rem;
        border-radius: 15px;
        backdrop-filter: blur(5px);
    }
    
    .header-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .header-subtitle {
        margin: 0.25rem 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }
    
    .info-banner {
        display: flex;
        gap: 1rem;
        background: rgba(var(--primary-rgb, 79, 70, 229), 0.05);
        padding: 1rem;
        border-radius: 12px;
        border-left: 4px solid var(--primary-500, #6366f1);
        margin-bottom: 2rem;
    }
    
    .info-icon {
        color: var(--primary-500);
        font-size: 1.2rem;
    }
    
    .info-text {
        font-size: 0.9rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }
    
    .form-premium {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
    
    .select-wrapper {
        position: relative;
    }
    
    .select-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--text-muted);
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        border: 1.5px solid var(--border-color, #e5e7eb);
        transition: all 0.3s;
        appearance: none;
    }
    
    .form-input:focus {
        border-color: var(--primary-500);
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
        outline: none;
    }
    
    .form-help {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
    }
    
    .participant-preview {
        margin-top: 0.5rem;
    }
    
    .preview-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        font-weight: 700;
    }
    
    .preview-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        background: var(--bg-alt, #f9fafb);
        border-radius: 15px;
        border: 1px dashed var(--border-color);
    }
    
    .participant-avatar {
        width: 48px;
        height: 48px;
        background: var(--primary-100, #e0e7ff);
        color: var(--primary-700, #4338ca);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        border-radius: 12px;
    }
    
    .participant-name {
        font-weight: 600;
        color: var(--text-primary);
    }
    
    .participant-email {
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    .event-tag {
        display: inline-block;
        font-size: 0.75rem;
        background: rgba(0,0,0,0.05);
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        margin-top: 0.4rem;
        color: var(--text-secondary);
    }
    
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
        cursor: pointer;
        border: none;
    }
    
    .btn-secondary {
        background: var(--bg-alt, #f3f4f6);
        color: var(--text-primary);
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    
    .btn-primary {
        background: var(--primary-600);
        color: white;
    }
    
    .btn-primary:hover {
        background: var(--primary-700);
        transform: translateY(-2px);
    }
    
    .btn-glow:hover {
        box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);
    }
</style>
@endsection
