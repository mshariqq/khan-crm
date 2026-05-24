<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    protected $actions = ['View ', 'Create ', 'Edit All ', 'Edit ', 'Delete All ', 'Delete ', 'Manage '];

    public function index(Request $request)
    {
        if (!Auth::user()->can('staff_view')) {
            abort(403, 'Unauthorized action.');
        }

        $groups = Permission::all()->map(function ($p) {
            foreach ($this->actions as $action) {
                if (str_starts_with($p->name, $action)) {
                    return trim(str_replace('All ', '', substr($p->name, strlen($action))));
                }
            }
            return $p->name;
        })->unique()->sort()->values();

        $query = Permission::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('group') && $request->group !== 'all') {
            $group = $request->group;
            $query->where(function ($q) use ($group) {
                foreach ($this->actions as $action) {
                    $q->orWhere('name', 'like', $action . $group);
                }
                $q->orWhere('name', $group);
            });
        }

        $permissions = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.permissions.index', compact('permissions', 'groups'));
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
