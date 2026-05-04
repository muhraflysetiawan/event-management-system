@extends('layouts.guest')
@section('title', 'Register')

@section('content')
<form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label class="form-label" for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-input" value="{{ old('name') }}" placeholder="John Doe" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@university.edu" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="role_id">Register As *</label>
        <select id="role_id" name="role_id" class="form-input" required @change="updateRole($event.target)">
            <option value="">Select your role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        <p style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">Note: Roles other than Student and Lecturer require Superadmin approval.</p>
    </div>
    <div class="form-group" id="sk_document_group" style="display: none;">
        <label class="form-label" for="sk_document">SK Document (PDF/JPG/PNG) *</label>
        <input type="file" id="sk_document" name="sk_document" class="form-input">
        <p style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">Required for Committee role verification.</p>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label class="form-label" for="student_id">ID Number (NIM/NIP) *</label>
            <input type="text" id="student_id" name="student_id" class="form-input" value="{{ old('student_id') }}" placeholder="e.g. 123456789" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="phone">Phone (optional)</label>
            <input type="text" id="phone" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+62 xxx">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <div class="password-group" x-data="passwordToggle()">
            <input :type="show ? 'text' : 'password'" id="password" name="password" class="form-input" placeholder="Min. 8 characters" required>
            <button type="button" class="password-toggle" @click="toggle()">
                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <div class="password-group" x-data="passwordToggle()">
            <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" required>
            <button type="button" class="password-toggle" @click="toggle()">
                <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg">
        <i class="fas fa-user-plus"></i> Create Account
    </button>

    <div class="guest-links">
        <a href="{{ route('login') }}">Already have an account? <strong>Sign In</strong></a>
    </div>
</form>

<script>
    function updateRole(select) {
        const selectedOption = select.options[select.selectedIndex];
        const slug = selectedOption.getAttribute('data-slug');
        const skGroup = document.getElementById('sk_document_group');
        const skInput = document.getElementById('sk_document');

        if (slug === 'committee') {
            skGroup.style.display = 'block';
            skInput.required = true;
        } else {
            skGroup.style.display = 'none';
            skInput.required = false;
        }
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        updateRole(document.getElementById('role_id'));
    });
</script>
@endsection
