<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemsGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemPaneController extends Controller
{
    public function create()
    {
        if (!Auth::user()->can('items_create')) {
            abort(403);
        }

        $groups = ItemsGroup::all();

        return view('admin.items.pane.create', compact('groups'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('items_edit')) {
            abort(403);
        }

        $item = Item::findOrFail($id);
        $groups = ItemsGroup::all();

        return view('admin.items.pane.edit', compact('item', 'groups'));
    }

    public function show($id)
    {
        if (!Auth::user()->can('items_view')) {
            abort(403);
        }

        $item = Item::with('group')->findOrFail($id);

        return view('admin.items.pane.show', compact('item'));
    }
}
