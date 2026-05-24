@extends('layouts.admin')

@section('breadcrumb', 'Knowledge Base')

@section('main')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Knowledge Base</h1>
            <p class="text-slate-500 text-sm mt-1">Manage knowledge base articles</p>
        </div>
        <a href="{{ route('admin.knowledgebase.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
            <i class="bi bi-plus-lg"></i>
            Add Article
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @php
        $groupedArticles = $articles->groupBy('articlegroup');
    @endphp

    @if($articles->count() > 0)
        @foreach(\App\Models\KnowledgeBaseGroup::where('active', 1)->orderBy('group_order')->get() as $group)
            @if(isset($groupedArticles[$group->groupid]) && $groupedArticles[$group->groupid]->count() > 0)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $group->color ?? '#6366f1' }}">
                            <i class="bi bi-folder-fill text-sm"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-slate-800">{{ $group->name }}</h2>
                        <span class="text-xs text-slate-500">({{ $groupedArticles[$group->groupid]->count() }} articles)</span>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <table class="w-full">
                            <tbody class="divide-y divide-slate-100">
                                @foreach($groupedArticles[$group->groupid]->sortBy('article_order') as $article)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <span class="font-medium text-slate-800">{{ $article->subject }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm text-slate-500">Order: {{ $article->article_order }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($article->active)
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-500">
                                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('admin.knowledgebase.edit', $article->articleid) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.knowledgebase.destroy', $article->articleid) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this article?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endforeach

        @if(isset($groupedArticles[null]) && $groupedArticles[null]->count() > 0)
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white bg-slate-400">
                        <i class="bi bi-folder text-sm"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800">Ungrouped</h2>
                    <span class="text-xs text-slate-500">({{ $groupedArticles[null]->count() }} articles)</span>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <table class="w-full">
                        <tbody class="divide-y divide-slate-100">
                            @foreach($groupedArticles[null]->sortBy('article_order') as $article)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-slate-800">{{ $article->subject }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm text-slate-500">Order: {{ $article->article_order }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($article->active)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-500">
                                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.knowledgebase.edit', $article->articleid) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.knowledgebase.destroy', $article->articleid) }}" method="POST" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this article?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($articles->hasPages())
            <div class="mt-6 px-4 py-3 bg-white rounded-xl border border-slate-200">
                {{ $articles->links() }}
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 rounded-full flex items-center justify-center">
                <i class="bi bi-book text-2xl text-slate-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 mb-2">No Knowledge Base Articles</h3>
            <p class="text-slate-500 text-sm mb-6 max-w-sm mx-auto">Create articles to help customers find answers to common questions.</p>
            <a href="{{ route('admin.knowledgebase.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all">
                <i class="bi bi-plus-lg"></i>
                Create First Article
            </a>
        </div>
    @endif
</div>
@endsection