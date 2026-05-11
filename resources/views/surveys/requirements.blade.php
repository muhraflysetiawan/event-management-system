@extends('layouts.app')
@section('title', 'Manage Requirements - ' . $event->title)

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Joining Requirements: {{ $event->title }}</h3>
    </div>
    <div class="card-body">
        <p class="mb-4 text-muted">Add questions that participants will answer before joining. The system will provide suggestions based on their answers, but they will not be prevented from joining.</p>
        
        <form action="{{ route('surveys.requirements.save', $event) }}" method="POST" id="requirements-form">
            @csrf
            
            <div id="requirements-container">
                <label class="form-label mb-2">Requirement Questions</label>
                @if(isset($requirements) && $requirements->count() > 0)
                    @foreach($requirements as $index => $req)
                        <div class="requirement-item mb-3 p-3" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                            <div class="row">
                                <div class="col-md-11 mb-2">
                                    <input type="text" name="requirements[{{ $index }}][text]" class="form-control" placeholder="e.g., Do you have a laptop?" value="{{ $req->question_text }}" required>
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
                                <input type="text" name="requirements[0][text]" class="form-control" placeholder="e.g., Do you have a laptop?" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-requirement" style="width: 100%;"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 2rem; flex-wrap: wrap; gap: 1rem;">
                <button type="button" id="add-requirement" class="btn btn-outline" style="background: #980517; color: white; border-color: #980517; margin-bottom: 0;"><i class="fas fa-plus"></i> Add Question</button>
                
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <a href="{{ route('events.show', $event) }}" class="btn btn-secondary" style="color: white;">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="color: white;">Save Requirements</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('requirements-container');
    const addButton = document.getElementById('add-requirement');
    let reqIndex = {{ isset($requirements) ? $requirements->count() : 1 }};

    addButton.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'requirement-item mb-3 p-3';
        div.style.background = 'var(--bg-card)';
        div.style.border = '1px solid var(--border-color)';
        div.style.borderRadius = 'var(--radius-md)';
        div.innerHTML = `
            <div class="row">
                <div class="col-md-11 mb-2">
                    <input type="text" name="requirements[${reqIndex}][text]" class="form-control" placeholder="e.g., Do you have a laptop?" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-requirement" style="width: 100%;"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        container.appendChild(div);
        reqIndex++;
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
