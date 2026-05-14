<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $roles = Role::whereIn('slug', ['student', 'lecturer'])->get();
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'student_id' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
            'sk_document' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
        ]);

        $role = Role::find($request->role_id);

        // Domain restriction for student and lecturer
        if (in_array($role->slug, ['student', 'lecturer'])) {
            if (!str_ends_with($request->email, '@krw.horizon.ac.id')) {
                return back()->withErrors(['email' => 'Students and Lecturers must use @krw.horizon.ac.id domain.'])->withInput();
            }
        }

        // SK Document requirement for committee
        if ($role->slug === 'committee' && !$request->hasFile('sk_document')) {
            return back()->withErrors(['sk_document' => 'SK Document is required for Committee role.'])->withInput();
        }

        $isActive = in_array($role->slug, ['student', 'lecturer']);

        $skPath = null;
        if ($request->hasFile('sk_document')) {
            $skPath = $request->file('sk_document')->store('sk_documents', 'public');
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'student_id' => $request->student_id,
            'phone' => $request->phone,
            'is_active' => $isActive,
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'sk_document' => $skPath,
        ]);

        \Illuminate\Support\Facades\Mail::raw("Your OTP for Horizon Event Management is: {$otp}. It will expire in 10 minutes.", function($msg) use ($user) {
            $msg->to($user->email)->subject('Verification OTP');
        });

        session(['verify_user_id' => $user->id]);

        return redirect()->route('otp.verify');
    }

    public function showOtpForm()
    {
        if (!session('verify_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp_code' => 'required|string|size:6']);
        
        $userId = session('verify_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::find($userId);
        
        if (!$user || $user->otp_code !== $request->otp_code || now()->gt($user->otp_expires_at)) {
            return back()->with('error', 'Invalid or expired OTP.');
        }

        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        session()->forget('verify_user_id');

        if (!$user->is_active) {
            return redirect()->route('login')->with('info', 'Your email is verified. Your account requires Superadmin approval before you can log in.');
        }

        auth()->login($user);
        return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to the system.');
    }
}
