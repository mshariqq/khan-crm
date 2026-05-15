<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('staff_view')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::with('staffRole')->paginate(15);
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::all();
        $departments = Department::all();

        return view('admin.staff.create', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('staff_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'email' => 'required|email|unique:tblstaff,email',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'role' => 'required|integer',
            'active' => 'required|integer',
            'departments' => 'nullable|array',
        ]);

        $staff = Staff::create([
            'email' => $request->email,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => $request->active,
            'datecreated' => now(),
            'admin' => $request->role == 1 ? 1 : 0, // Assuming role 1 is admin
        ]);

        if ($request->has('departments')) {
            $staff->departments()->attach($request->departments);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::with('departments')->findOrFail($id);
        $roles = Role::all();
        $departments = Department::all();

        return view('admin.staff.edit', compact('staff', 'roles', 'departments'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('staff_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'email' => 'required|email|unique:tblstaff,email,' . $id . ',staffid',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'role' => 'required|integer',
            'active' => 'required|integer',
            'departments' => 'nullable|array',
        ]);

        $staff = Staff::findOrFail($id);
        
        $updateData = [
            'email' => $request->email,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'role' => $request->role,
            'active' => $request->active,
            'admin' => $request->role == 1 ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $staff->update($updateData);

        if ($request->has('departments')) {
            $staff->departments()->sync($request->departments);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('staff_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $staff = Staff::findOrFail($id);

        if ($staff->staffid === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully!');
    }
}
