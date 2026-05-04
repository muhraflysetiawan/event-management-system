@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
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
                <select name="role_id" id="role-select" class="form-input" required onchange="toggleOrganization()">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
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

        <div class="form-row" id="organization-group" style="display: {{ (isset($user->role) && $user->role->slug === 'external') || old('organization') ? 'grid' : 'none' }};">
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Organization (For External Users)</label>
                <input type="text" name="organization" class="form-input" value="{{ old('organization', $user->organization) }}">
            </div>
        </div>


        <div class="form-divider" style="margin: 1.5rem 0; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1rem;">Leave password fields blank if you don't want to change it.</p>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="password-group" x-data="passwordToggle()">
                    <input :type="show ? 'text' : 'password'" name="password" class="form-input">
                    <button type="button" class="password-toggle" @click="toggle()">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <div class="password-group" x-data="passwordToggle()">
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" class="form-input">
                    <button type="button" class="password-toggle" @click="toggle()">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>
        </div>

        <div style="margin-top:1.5rem;display:flex;justify-content:flex-end;gap:0.75rem;">
            <a href="{{ route('admin.users.index') }}" class="btn" style="background:#473f3d;color:white;border:none;"><i class="fas fa-arrow-left"></i> Back</a>
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>

<script>
function toggleOrganization() {
    const select = document.getElementById('role-select');
    const selectedOption = select.options[select.selectedIndex];
    const roleSlug = selectedOption ? selectedOption.getAttribute('data-slug') : '';
    const orgGroup = document.getElementById('organization-group');
    
    if (roleSlug === 'external') {
        orgGroup.style.display = 'grid'; // because it's a form-row
    } else {
        orgGroup.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', toggleOrganization);
</script>
@endsection
