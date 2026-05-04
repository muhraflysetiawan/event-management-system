@extends('layouts.app')
@section('title', 'Application Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">General Settings</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label" for="app_name">Application Name</label>
                <input type="text" id="app_name" name="app_name" class="form-input" value="{{ $appName }}" required>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">This name will be displayed in the header and emails.</p>
            </div>

            <div class="form-group mt-3">
                <label class="form-label" for="website_logo">Website Logo</label>
                <div style="display:flex; align-items:center; gap:1.5rem; margin-top:0.5rem;">
                    @php $logo = \App\Models\Setting::get('website_logo'); @endphp
                    <div style="width:60px; height:60px; background:var(--bg-sidebar); border-radius:8px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border-color); overflow:hidden;">
                        @if($logo)
                            <img src="{{ asset('storage/' . $logo) }}" style="max-width:100%; max-height:100%; object-fit:contain;">
                        @else
                            <i class="fas fa-image" style="color:var(--text-muted); font-size:1.5rem;"></i>
                        @endif
                    </div>
                    <div style="flex:1;">
                        <input type="file" id="website_logo" name="website_logo" class="form-input" accept="image/*">
                        <p style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">Upload a square logo (recommended 512x512px). Replaces Sidebar, Login, and Register logos.</p>
                    </div>
                </div>
            </div>

            <div class="action-group" style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
