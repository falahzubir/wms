<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     * @param none
     * @return view
     */
    public function index()
    {
        if (auth()->user()->hasPermissionTo('user.list')) {
            return view('users.index', [
                'title' => 'Users',
                'users' => User::with(['roles'])->get()
            ]);
        } else {
            return redirect()->route('profile');
        }
    }

    /**
     * Create user page.
     * @param none
     * @return view
     */
    public function create()
    {
        return view('users.create', [
            'title' => 'Create User',
            'roles' => Role::all()
        ]);
    }

    /**
     * Store user.
     * @param Request $request
     * @return redirect to users
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:10',
            'staff_id' => 'nullable|unique:users',
            'password' => 'required|min:6|max:10',
        ]);

        $user = User::create($request->all());
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * Edit user page.
     * @param User $user
     * @return view
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'title' => "Edit User: $user->name",
            'roles' => Role::all(),
            'user' => $user
        ]);
    }

    /**
     * Update user.
     * @param Request $request
     * @param User $user
     * @return redirect to users
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|min:3|max:10',
            'staff_id' => 'nullable|unique:users,staff_id,' . $user->id,
        ]);

        $user->update($request->all());

        $user->syncRoles($request->role);

        return redirect()->route('users.index')->with('success', "User {$user->name} updated successfully");
    }

    /**
     * View user profile.
     * @param none
     * @return view
     */
    public function profile()
    {
        return view('users.profile', [
            'title' => 'Profile',
            'user' => auth()->user()
        ]);
    }
}
