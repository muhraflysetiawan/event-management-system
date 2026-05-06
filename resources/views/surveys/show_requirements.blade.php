@extends('layouts.app')
@section('title', 'Event Requirements - ' . $event->title)

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Join Event: {{ $event->title }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4">Please answer the following questions to check if you meet the criteria for this event.</p>
        
        <form action="{{ route('surveys.requirements.submit', $event) }}" method="POST">
            @csrf
            
            @foreach($requirements as $req)
                <div class="mb-4 p-4" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <p style="font-weight: 600; font-size: 1.1rem; margin: 0;">{{ $req->question_text }}</p>
                    </div>
                    <div style="display: flex; gap: 2rem;">
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="radio" name="answers[{{ $req->id }}]" value="yes" required style="width: 1.2rem; height: 1.2rem;"> 
                            <span>Yes</span>
                        </label>
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="radio" name="answers[{{ $req->id }}]" value="no" required style="width: 1.2rem; height: 1.2rem;"> 
                            <span>No</span>
                        </label>
                    </div>
                </div>
            @endforeach

            <div class="alert alert-info mb-4" style="background: rgba(var(--primary-rgb), 0.1); border: 1px solid var(--primary-color); color: var(--text-primary);">
                <i class="fas fa-info-circle"></i> Your answers will be used by the system to provide suggestions. They will <strong>not</strong> prevent you from joining the event.
            </div>

            <div class="action-group" style="justify-content: space-between;">
                <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg">Submit & Join</button>
            </div>
        </form>
    </div>
</div>
@endsection
