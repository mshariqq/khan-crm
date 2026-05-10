<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('staff_view')) {
            abort(403, 'Unauthorized action.');
        }

        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tblpermissions,slug',
        ]);

        Permission::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tblpermissions,slug,' . $id,
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('staff_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully!');
    }
}
