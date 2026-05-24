<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    /** Display a listing of the knowledge base articles. */
    public function index()
    {
        if (!Auth::user()->can('knowledge_base_view')) abort(403);
        $articles = KnowledgeBase::orderBy('article_order')
            ->paginate(15);
        return view('admin.knowledge_base.index', compact('articles'));
    }

    /** Show the form for creating a new article. */
    public function create()
    {
        if (!Auth::user()->can('knowledge_base_create')) abort(403);
        $groups = KnowledgeBaseGroup::orderBy('name')->pluck('name', 'groupid');
        return view('admin.knowledge_base.create', compact('groups'));
    }

    /** Store a newly created article in storage. */
    public function store(Request $request)
    {
        if (!Auth::user()->can('knowledge_base_create')) abort(403);
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'articlegroup' => 'nullable|integer|exists:tblknowledge_base_groups,groupid',
            'active' => 'required|integer',
            'article_order' => 'required|integer',
        ]);

        $validated['slug'] = Str::slug($validated['subject']);
        $validated['active'] = $request->input('active', 1);
        $validated['datecreated'] = now();
        $validated['staff_article'] = auth()->user()->staffid ?? 0;

        KnowledgeBase::create($validated);
        return redirect()->route('admin.knowledgebase.index')->with('success', 'Article created successfully');
    }

    /** Show the form for editing the specified article. */
    public function edit($id)
    {
        if (!Auth::user()->can('knowledge_base_edit')) abort(403);
        $article = KnowledgeBase::findOrFail($id);
        $groups = KnowledgeBaseGroup::orderBy('name')->pluck('name', 'groupid');
        return view('admin.knowledge_base.edit', compact('article', 'groups'));
    }

    /** Update the specified article in storage. */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('knowledge_base_edit')) abort(403);
        $article = KnowledgeBase::findOrFail($id);
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'articlegroup' => 'nullable|integer|exists:tblknowledge_base_groups,groupid',
            'active' => 'required|integer',
            'article_order' => 'required|integer',
        ]);

        $validated['slug'] = Str::slug($validated['subject']);
        $validated['active'] = $request->input('active', 1);
        $article->update($validated);
        return redirect()->route('admin.knowledgebase.index')->with('success', 'Article updated successfully');
    }

    /** Remove the specified article from storage. */
    public function destroy($id)
    {
        if (!Auth::user()->can('knowledge_base_delete')) abort(403);
        $article = KnowledgeBase::findOrFail($id);
        $article->delete();
        return redirect()->route('admin.knowledgebase.index')->with('success', 'Article deleted successfully');
    }

    /** Display the specified article – optional for admin view. */
    public function show($id)
    {
        if (!Auth::user()->can('knowledge_base_view')) abort(403);
        $article = KnowledgeBase::findOrFail($id);
        return view('admin.knowledge_base.show', compact('article'));
    }
}
