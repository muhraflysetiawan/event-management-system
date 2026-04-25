@extends('layouts.guest')
@section('title', 'Verify Email')

@section('content')
<div style="text-align: center;">
    <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--primary-400);">
        <i class="fas fa-envelope-open-text"></i>
    </div>
    <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">Check your email</h2>
    <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 1.5rem;">
        We've sent a verification link to <strong style="color: var(--text-secondary);">{{ auth()->user()->email }}</strong>. Please click the link to verify your account.
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-redo"></i> Resend Verification Email
        </button>
    </form>

    <div class="guest-links mt-2">
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:none;border:none;color:var(--text-secondary);cursor:pointer;font-size:0.875rem;">
                <i class="fas fa-sign-out-alt"></i> Sign out
            </button>
        </form>
    </div>
</div>
@endsection
