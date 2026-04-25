@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="you@university.edu" required autofocus>
    </div>

    <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
    </div>

    <div class="form-check">
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Remember me</label>
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg">
        <i class="fas fa-sign-in-alt"></i> Sign In
    </button>

    <div class="guest-links">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
        <a href="{{ route('register') }}">Don't have an account? <strong>Register</strong></a>
    </div>
</form>
@endsection
