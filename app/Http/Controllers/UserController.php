<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'regiment_no' => 'nullable|string|max:20',
            'rank' => 'nullable|string|max:50',
            'contact_no' => 'nullable|string|max:20',
            'establishment_id' => 'nullable|exists:establishments,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'regiment_no' => $request->regiment_no,
            'rank' => $request->rank,
            'contact_no' => $request->contact_no,
            'establishment_id' => $request->establishment_id,
            'is_active' => $request->is_active,
            'email_verified_at' => now(),
        ]);

        // Assign roles to user
        $roles = Role::whereIn('id', $request->roles)->get();
        $user->assignRole($roles);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully and assigned roles.');
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
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'regiment_no' => 'nullable|string|max:20',
            'rank' => 'nullable|string|max:50',
            'contact_no' => 'nullable|string|max:20',
            'establishment_id' => 'nullable|exists:establishments,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
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
