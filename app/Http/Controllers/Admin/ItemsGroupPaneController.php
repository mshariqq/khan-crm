<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemsGroup;
use Illuminate\Http\Request;

class ItemsGroupPaneController extends Controller
{
    public function create()
    {
        if (!auth()->user()->can('itemsgroups_create')) {
            abort(403);
        }

        return view('admin.itemsgroups.pane.create');
    }

    public function edit($id)
    {
        if (!auth()->user()->can('itemsgroups_edit')) {
            abort(403);
        }

        $group = ItemsGroup::findOrFail($id);

        return view('admin.itemsgroups.pane.edit', compact('group'));
    }

    public function show($id)
    {
        if (!auth()->user()->can('itemsgroups_view')) {
            abort(403);
        }

        $group = ItemsGroup::withCount('items')->findOrFail($id);

        return view('admin.itemsgroups.pane.show', compact('group'));
    }
}
