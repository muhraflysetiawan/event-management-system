@extends('layouts.guest')
@section('title', 'Verify OTP')

@section('content')
<div style="text-align: center; margin-bottom: 2rem;">
    <h3 style="margin-bottom: 0.5rem;">Email Verification</h3>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">
        We have sent a 6-digit OTP to your email address. <br>
        Please enter it below to verify your account.
    </p>
</div>

<form method="POST" action="{{ route('otp.verify.post') }}">
    @csrf
    <div class="form-group" style="text-align: center;">
        <label class="form-label" for="otp_code">Enter OTP</label>
        <input type="text" id="otp_code" name="otp_code" class="form-input" style="font-size: 1.5rem; letter-spacing: 0.5rem; text-align: center;" maxlength="6" placeholder="------" required autofocus>
        @error('otp_code')
            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1.5rem;">
        <i class="fas fa-check-circle"></i> Verify OTP
    </button>
</form>

<div class="guest-links" style="margin-top: 1.5rem; text-align: center;">
    <a href="{{ route('login') }}">Back to Login</a>
</div>
@endsection
