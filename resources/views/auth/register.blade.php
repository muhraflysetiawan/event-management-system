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

    <input type="hidden" name="role_id" value="1">
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


@endsection
