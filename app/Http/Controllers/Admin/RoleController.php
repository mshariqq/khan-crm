<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('staff_view')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::orderBy('name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        $availablePermissions = \App\Models\Permission::all();

        return view('admin.roles.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:tblroles,name',
            'permissions' => 'required|array',
        ]);

        Role::create([
            'name' => $request->name,
            'permissions' => implode(',', $request->permissions),
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $role = Role::findOrFail($id);
        $availablePermissions = \App\Models\Permission::all();

        return view('admin.roles.edit', compact('role', 'availablePermissions'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:tblroles,name,' . $id . ',roleid',
            'permissions' => 'required|array',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'permissions' => implode(',', $request->permissions),
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('staff_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $role = Role::findOrFail($id);
        
        // Prevent deleting Admin role if it's the only one
        if ($role->name === 'Admin') {
            return redirect()->back()->with('error', 'The Admin role cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully!');
    }
}
