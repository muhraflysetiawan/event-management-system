@extends('layouts.guest')
@section('title', 'Forgot Password')

@section('content')
<p style="color: var(--text-muted); text-align: center; margin-bottom: 1.5rem; font-size: 0.875rem;">
    Enter your email address and we'll send you a link to reset your password.
</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@university.edu" required autofocus>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg">
        <i class="fas fa-paper-plane"></i> Send Reset Link
    </button>

    <div class="guest-links">
        <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to login</a>
    </div>
</form>
@endsection
