<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->can('leave_types_view')) abort(403);

        $leaveTypes = LeaveType::orderBy('name')->paginate(15);
        return view('admin.leave_types.index', compact('leaveTypes'));
    }

    public function create()
    {
        if (!Auth::user()->can('leave_types_create')) abort(403);
        return view('admin.leave_types.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('leave_types_create')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255|unique:tblleave_types,name',
            'default_quota' => 'required|integer|min:0',
            'is_paid' => 'required|boolean',
        ]);

        LeaveType::create([
            'name' => $request->name,
            'default_quota' => $request->default_quota,
            'is_paid' => $request->boolean('is_paid'),
        ]);

        return redirect()->route('admin.leave-types.index')->with('success', 'Leave type created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('leave_types_edit')) abort(403);
        $leaveType = LeaveType::findOrFail($id);
        return view('admin.leave_types.edit', compact('leaveType'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('leave_types_edit')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255|unique:tblleave_types,name,' . $id,
            'default_quota' => 'required|integer|min:0',
            'is_paid' => 'required|boolean',
        ]);

        $leaveType = LeaveType::findOrFail($id);
        $leaveType->update([
            'name' => $request->name,
            'default_quota' => $request->default_quota,
            'is_paid' => $request->boolean('is_paid'),
        ]);

        return redirect()->route('admin.leave-types.index')->with('success', 'Leave type updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('leave_types_delete')) abort(403);
        LeaveType::findOrFail($id)->delete();

        return redirect()->route('admin.leave-types.index')->with('success', 'Leave type deleted successfully!');
    }
}
