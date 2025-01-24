<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return Permission::all();
    }

    public function store(Request $request)
    {
        $permission = Permission::create($request->validate([
            'name' => 'required|unique:permissions|min:3',
        ]));

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission,
        ], 201);
    }

    public function update(Request $request, Permission $permission)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:permissions|min:3',
        ]);
        $permission = Permission::where('id', $permission->id)->update($validatedData);

        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => $permission,
        ]);
    }

    public function destroy(Permission $permission)
    {
        Permission::where('id', $permission->id)->delete();

        return response()->json([
            'message' => 'Permission deleted successfully',
        ]);
    }
}
