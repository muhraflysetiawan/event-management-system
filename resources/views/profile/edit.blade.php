@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="card mb-4" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Profile Information</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', auth()->user()->name) }}" required>
                    @error('name')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address (Cannot be changed)</label>
                    <input type="email" class="form-input" value="{{ auth()->user()->email }}" disabled style="background-color: var(--bg-hover);">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-input" value="{{ auth()->user()->role->name ?? 'User' }}" disabled style="background-color: var(--bg-hover);">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', auth()->user()->phone) }}">
                    @error('phone')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Registration/Student ID (Optional)</label>
                    <input type="text" name="student_id" class="form-input" value="{{ old('student_id', auth()->user()->student_id) }}">
                    @error('student_id')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Department (Optional)</label>
                    <input type="text" name="department" class="form-input" value="{{ old('department', auth()->user()->department) }}">
                    @error('department')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Organization (Optional)</label>
                <input type="text" name="organization" class="form-input" value="{{ old('organization', auth()->user()->organization) }}">
                @error('organization')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
            </div>

            <div class="action-group mt-2" style="justify-content:flex-start;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Password Card -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Change Password</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-input" required>
                @error('current_password')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input" required>
                    @error('password')<div style="color:var(--danger); font-size:0.875rem; margin-top:0.25rem;">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>

            <div class="action-group mt-2" style="justify-content:flex-start;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> Update Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
