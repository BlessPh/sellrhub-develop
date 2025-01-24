<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use \Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        return Role::whereNotIn('name', ['super_admin'])->get();
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:roles|string|min:3',
        ]);

        $roles = Role::create($validatedData);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $roles
        ]);
    }

    public function update(Request $request, $role) {
        if ($role == 'super_admin') {
            return response()->json([
                'message' => 'You can\'t update super admin role',
            ], 403);
        }
        $validatedData = $request->validate([
            'name' => 'required|string|min:3'
        ]);

        $role = Role::findOrFail($role)->update($validatedData);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    public function destroy($role) {
        if ($role == 'super_admin') {
            return response()->json([
                'message' => 'You can\'t delete super admin role',
            ], 401);
        }

        $role = Role::findOrFail($role)->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }
}
