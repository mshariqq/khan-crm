<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBaseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    /** Display a listing of the knowledge base articles. */
    public function index()
    {
        $articles = KnowledgeBase::orderBy('article_order')
            ->paginate(15);
        return view('admin.knowledge_base.index', compact('articles'));
    }

    /** Show the form for creating a new article. */
    public function create()
    {
        $groups = KnowledgeBaseGroup::orderBy('name')->pluck('name', 'groupid');
        return view('admin.knowledge_base.create', compact('groups'));
    }

    /** Store a newly created article in storage. */
    public function store(Request $request)
    {
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
        $article = KnowledgeBase::findOrFail($id);
        $groups = KnowledgeBaseGroup::orderBy('name')->pluck('name', 'groupid');
        return view('admin.knowledge_base.edit', compact('article', 'groups'));
    }

    /** Update the specified article in storage. */
    public function update(Request $request, $id)
    {
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
        $article = KnowledgeBase::findOrFail($id);
        $article->delete();
        return redirect()->route('admin.knowledgebase.index')->with('success', 'Article deleted successfully');
    }

    /** Display the specified article – optional for admin view. */
    public function show($id)
    {
        $article = KnowledgeBase::findOrFail($id);
        return view('admin.knowledge_base.show', compact('article'));
    }
}
