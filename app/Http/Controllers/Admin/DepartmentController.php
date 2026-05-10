<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('departments_view')) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::orderBy('name', 'asc')->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        if (!Auth::user()->can('departments_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('departments_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:tbldepartments,name',
            'email' => 'nullable|email|max:100',
            'hidefromclient' => 'boolean',
        ]);

        $maxId = DB::table('tbldepartments')->max('departmentid') ?? 0;
        $departmentId = $maxId + 1;

        Department::create([
            'departmentid' => $departmentId,
            'name' => $request->name,
            'email' => $request->email,
            'hidefromclient' => $request->boolean('hidefromclient'),
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('departments_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $department = Department::findOrFail($id);
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('departments_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:tbldepartments,name,' . $id . ',departmentid',
            'email' => 'nullable|email|max:100',
            'hidefromclient' => 'boolean',
        ]);

        $department = Department::findOrFail($id);
        $department->update([
            'name' => $request->name,
            'email' => $request->email,
            'hidefromclient' => $request->boolean('hidefromclient'),
        ]);

        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('departments_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $department = Department::findOrFail($id);
        
        // Check if department has tickets or staff to prevent orphans (optional but recommended)
        if ($department->tickets()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete department with existing tickets.');
        }

        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Department deleted successfully!');
    }
}
