<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemsGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsGroupController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('itemsgroups_view')) {
            abort(403, 'Unauthorized action.');
        }

        $groups = ItemsGroup::paginate(15);
        return view('admin.itemsgroups.index', compact('groups'));
    }

    public function create()
    {
        if (!Auth::user()->can('itemsgroups_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.itemsgroups.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('itemsgroups_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ItemsGroup::create($request->all());

        return redirect()->route('admin.itemsgroups.index')->with('success', 'Items Group created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('itemsgroups_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $group = ItemsGroup::findOrFail($id);
        return view('admin.itemsgroups.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('itemsgroups_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = ItemsGroup::findOrFail($id);
        $group->update($request->all());

        return redirect()->route('admin.itemsgroups.index')->with('success', 'Items Group updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('itemsgroups_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $group = ItemsGroup::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.itemsgroups.index')->with('success', 'Items Group deleted successfully!');
    }
}
