<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'student_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'student_id' => $request->student_id,
            'department' => $request->department,
            'organization' => $request->organization,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
