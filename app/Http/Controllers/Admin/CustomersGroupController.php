<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomersGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomersGroupController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('clients_view')) {
            abort(403, 'Unauthorized action.');
        }

        $groups = CustomersGroup::orderBy('name')->paginate(15);
        return view('admin.customer-groups.index', compact('groups'));
    }

    public function create()
    {
        if (!Auth::user()->can('clients_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.customer-groups.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('clients_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:191|unique:tblcustomers_groups,name',
        ]);

        CustomersGroup::create([
            'id' => DB::table('tblcustomers_groups')->max('id') + 1, // Assuming non-auto-increment based on migration
            'name' => $request->name,
        ]);

        return redirect()->route('admin.customer-groups.index')->with('success', 'Client group created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('clients_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $group = CustomersGroup::findOrFail($id);
        return view('admin.customer-groups.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('clients_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:191|unique:tblcustomers_groups,name,' . $id,
        ]);

        $group = CustomersGroup::findOrFail($id);
        $group->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.customer-groups.index')->with('success', 'Client group updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('clients_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $group = CustomersGroup::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.customer-groups.index')->with('success', 'Client group deleted successfully!');
    }
}
