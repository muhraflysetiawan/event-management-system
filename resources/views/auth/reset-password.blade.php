@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', $email ?? '') }}" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">New Password</label>
        <input type="password" id="password" name="password" class="form-input" placeholder="Min. 8 characters" required>
    </div>

    <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg">
        <i class="fas fa-key"></i> Reset Password
    </button>
</form>
@endsection
