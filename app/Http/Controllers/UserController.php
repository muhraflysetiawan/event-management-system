<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
            'department' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        AuditHelper::log('create_user', $user, null, $user->toArray());

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
            'department' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldData = $user->toArray();
        $user->update($validated);

        AuditHelper::log('update_user', $user, $oldData, $user->toArray());

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function toggleStatus(User $user)
    {
        $oldData = $user->toArray();
        $user->update(['is_active' => !$user->is_active]);
        
        AuditHelper::log('toggle_user_status', $user, $oldData, $user->toArray());

        return back()->with('success', 'User status updated successfully!');
    }
}
