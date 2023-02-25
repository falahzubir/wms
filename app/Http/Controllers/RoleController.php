<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     * @param none
     * return view
     */

    public function index()
    {
        return view('roles.index', [
            'title' => 'Permissions',
            'roles' => Role::all()
        ]);
    }

    /**
     * Create role page.
     * @param none
     * @return view
     */
    public function create()
    {
        return view('roles.create', [
            'title' => 'Create Role'
        ]);
    }

    /**
     * Create role.
     * @param Request $request
     * @return redirect to permissions
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:10',
        ]);

        $role = Role::create($request->all());

        return redirect()->route('permissions.edit', $role->id)->with('success', 'Role created successfully');
    }
}
