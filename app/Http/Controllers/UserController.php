<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Establishment;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:System Administrator (DMOV)']);
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with(['roles', 'establishment'])->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        $establishments = Establishment::where('is_active', true)->orderBy('name')->get();
        return view('users.create', compact('roles', 'establishments'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'e_no' => 'required|string|max:255|unique:users',
            'regiment_no' => 'nullable|string|max:20',
            'rank' => 'nullable|string|max:50',
            'contact_no' => 'nullable|string|max:20',
            'establishment_id' => 'required|exists:establishments,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        // Check if System Administrator role is being assigned
        $roles = Role::whereIn('id', $request->roles)->get();
        $isSystemAdmin = $roles->contains(function ($role) {
            return $role->name === 'System Administrator (DMOV)';
        });

        $user = User::create([
            'name' => $request->name,
            'e_no' => $request->e_no,
            'email' => $isSystemAdmin ? $request->e_no : $request->e_no . '@authority.army.lk',
            'password' => Hash::make($isSystemAdmin ? 'admin123' : 'temp_password_' . time()),
            'regiment_no' => $request->regiment_no,
            'rank' => $request->rank,
            'contact_no' => $request->contact_no,
            'establishment_id' => $request->establishment_id,
            'is_active' => $request->is_active,
            'email_verified_at' => now(),
        ]);

        // Assign roles to user
        $user->assignRole($roles);

        $successMessage = 'User created successfully and assigned roles.';
        if ($isSystemAdmin) {
            $successMessage .= ' System Administrator login: Email = ' . $user->email . ' | Password = admin123';
        }

        return redirect()->route('users.index')
            ->with('success', $successMessage);
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $establishments = Establishment::where('is_active', true)->orderBy('name')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('users.edit', compact('user', 'roles', 'establishments', 'userRoles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Custom validation: If user has System Administrator role, it must be included
        $systemAdminRole = Role::where('name', 'System Administrator (DMOV)')->first();
        if ($user->hasRole('System Administrator (DMOV)') && $systemAdminRole) {
            if (!in_array($systemAdminRole->id, $request->roles ?? [])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['roles' => 'System Administrator role cannot be removed from this user.']);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'e_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'regiment_no' => 'nullable|string|max:20',
            'rank' => 'nullable|string|max:50',
            'contact_no' => 'nullable|string|max:20',
            'establishment_id' => 'required|exists:establishments,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'e_no' => $request->e_no,
            'email' => $request->e_no . '@authority.army.lk', // Update email based on E No
            'regiment_no' => $request->regiment_no,
            'rank' => $request->rank,
            'contact_no' => $request->contact_no,
            'establishment_id' => $request->establishment_id,
            'is_active' => $request->is_active,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Update user roles
        $roles = Role::whereIn('id', $request->roles)->get();

        // If user currently has System Administrator role, ensure it's preserved
        if ($user->hasRole('System Administrator (DMOV)')) {
            $systemAdminRole = Role::where('name', 'System Administrator (DMOV)')->first();
            if ($systemAdminRole && !$roles->contains($systemAdminRole)) {
                $roles->push($systemAdminRole);
            }
        }

        $user->syncRoles($roles);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting the current logged-in user
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Password reset successfully.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully.");
    }
}
