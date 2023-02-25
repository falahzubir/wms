<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Edit page for the permission.
     * @param Role $role
     * @return view
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $current_permissions = $role->with('permissions')->get();

        return view('permissions.edit', [
            'title' => $role->name,
            'role' => $role,
            'permissions' => $permissions,
            'current_permissions' => $current_permissions
        ]);
    }

    /**
     * Update the permission.
     * @param Request $request
     * @return json
     */
    public function update(Role $role, Request $request)
    {
        $permission = Permission::where('name', $request->permission)->first();
        if($request->checked == true){
            $role->givePermissionTo($permission);
        } else {
            $role->revokePermissionTo($permission);
        }

        return response()->json(['success' => 'OK']);
    }

    /**
     * Create a new permission.
     * @param Request $request
     * @return json
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'guard_name' => 'required|min:3',
        ]);

        $permission = Permission::create($request->all());

        return response()->json(['success' => 'OK']);
    }
}
