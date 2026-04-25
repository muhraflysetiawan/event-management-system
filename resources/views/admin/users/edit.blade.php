@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<div class="section-header">
    <h3 class="section-title">Edit User: {{ $user->name }}</h3>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-input" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-input" required>
                    <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-input" value="{{ old('department', $user->department) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Organization</label>
                <input type="text" name="organization" class="form-input" value="{{ old('organization', $user->organization) }}">
            </div>
        </div>

        <div class="form-divider" style="margin: 1.5rem 0; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1rem;">Leave password fields blank if you don't want to change it.</p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
        </div>

        <div style="margin-top:1.5rem;display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>
@endsection
