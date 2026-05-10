<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseGroupController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('knowledgebase_view')) {
            abort(403, 'Unauthorized action.');
        }

        $groups = KnowledgeBaseGroup::orderBy('group_order')->paginate(15);
        return view('admin.knowledge_base_groups.index', compact('groups'));
    }

    public function create()
    {
        if (!Auth::user()->can('knowledgebase_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.knowledge_base_groups.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('knowledgebase_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
            'group_order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
        ]);

        $validated['active'] = $request->has('active') ? 1 : 0;
        $validated['group_slug'] = strtolower(str_replace(' ', '-', $validated['name']));

        KnowledgeBaseGroup::create($validated);

        return redirect()->route('admin.knowledge-base-groups.index')->with('success', 'Knowledge base group created successfully');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('knowledgebase_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $group = KnowledgeBaseGroup::findOrFail($id);
        return view('admin.knowledge_base_groups.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('knowledgebase_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $group = KnowledgeBaseGroup::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
            'group_order' => 'nullable|integer',
            'color' => 'nullable|string|max:20',
        ]);

        $validated['active'] = $request->has('active') ? 1 : 0;
        $validated['group_slug'] = strtolower(str_replace(' ', '-', $validated['name']));

        $group->update($validated);

        return redirect()->route('admin.knowledge-base-groups.index')->with('success', 'Knowledge base group updated successfully');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('knowledgebase_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $group = KnowledgeBaseGroup::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.knowledge-base-groups.index')->with('success', 'Knowledge base group deleted successfully');
    }
}