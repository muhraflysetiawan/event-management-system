@extends('layouts.app')
@section('title', 'Manage Requirements - ' . $event->title)

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Joining Requirements: {{ $event->title }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4 text-muted">Add "Yes/No" questions that participants must answer before joining. If they answer "No" to any of these questions, they will be automatically rejected.</p>
        
        <form action="{{ route('surveys.requirements.save', $event) }}" method="POST" id="requirements-form">
            @csrf
            
            <div id="requirements-container">
                <label class="form-label mb-2">Requirement Questions (Must be answered "Yes" to join)</label>
                @if(isset($requirements) && $requirements->count() > 0)
                    @foreach($requirements as $index => $req)
                        <div class="requirement-item mb-3 p-3" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <div class="row">
                                <div class="col-md-11 mb-2">
                                    <input type="text" name="requirements[]" class="form-control" placeholder="e.g., Do you have a laptop?" value="{{ $req->question_text }}" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger remove-requirement" style="width: 100%;"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="requirement-item mb-3 p-3" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                        <div class="row">
                            <div class="col-md-11 mb-2">
                                <input type="text" name="requirements[]" class="form-control" placeholder="e.g., Do you have a laptop?" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-requirement" style="width: 100%;"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <button type="button" id="add-requirement" class="btn btn-outline mb-4"><i class="fas fa-plus"></i> Add Requirement</button>

            <div class="action-group" style="justify-content: flex-end;">
                <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Requirements</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('requirements-container');
    const addButton = document.getElementById('add-requirement');

    addButton.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'requirement-item mb-3 p-3';
        div.style.background = 'var(--bg-card)';
        div.style.border = '1px solid var(--border-color)';
        div.style.borderRadius = 'var(--radius-md)';
        div.innerHTML = `
            <div class="row">
                <div class="col-md-11 mb-2">
                    <input type="text" name="requirements[]" class="form-control" placeholder="e.g., Do you have a laptop?" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-requirement" style="width: 100%;"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        container.appendChild(div);
        attachRemoveEvent(div.querySelector('.remove-requirement'));
    });

    function attachRemoveEvent(button) {
        button.addEventListener('click', function() {
            button.closest('.requirement-item').remove();
        });
    }

    container.querySelectorAll('.remove-requirement').forEach(attachRemoveEvent);
});
</script>
@endsection
