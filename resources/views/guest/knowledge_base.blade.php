@extends('layouts.guest')

@section('title', 'Knowledge Base')

@section('main')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 py-8 md:py-12">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-4">How can we help you?</h1>
            <p class="text-gray-600 text-center mb-8 text-sm md:text-base">Search our knowledge base for answers</p>
            
            <form action="{{ route('kb') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="search" name="q" placeholder="Search for articles..." value="{{ request('q') }}" 
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 md:py-4 text-base md:text-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-24 md:pr-28">
                    <input type="hidden" name="group" value="{{ request('group') }}">
                    <button type="submit" class="absolute right-1.5 top-1.5 md:right-2 md:top-2 bg-indigo-600 px-4 md:px-6 py-2 rounded-lg text-white font-medium hover:bg-indigo-700 transition text-sm md:text-base">
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-6 md:py-8">
        @if(request('q') || request('group'))
            <div class="mb-6 flex items-center justify-between flex-wrap gap-2">
                <p class="text-gray-600 text-sm md:text-base">
                    @if(request('q'))
                        Showing results for "<strong>{{ request('q') }}</strong>"
                    @elseif(request('group'))
                        Showing category: "<strong>{{ \App\Models\KnowledgeBaseGroup::find(request('group'))->name ?? '' }}</strong>"
                    @endif
                    ({{ $articles->count() }} results)
                </p>
                <a href="{{ route('kb') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    Clear filters
                </a>
            </div>
        @endif

        @php
            $groupedArticles = $articles->groupBy('articlegroup');
        @endphp

        @if($articles->count() > 0)
            <div class="grid gap-4 md:gap-6">
                @foreach(\App\Models\KnowledgeBaseGroup::where('active', 1)->orderBy('group_order')->get() as $group)
                    @if(isset($groupedArticles[$group->groupid]) && $groupedArticles[$group->groupid]->count() > 0)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="bg-indigo-50 px-4 md:px-6 py-3 flex items-center justify-between border-b border-indigo-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 md:w-8 h-6 md:h-8 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $group->color ?? '#6366f1' }}">
                                        <i class="bi bi-folder-fill text-xs md:text-sm"></i>
                                    </div>
                                    <h2 class="font-semibold text-gray-800 text-sm md:text-base">{{ $group->name }}</h2>
                                </div>
                                <a href="{{ route('kb') }}?group={{ $group->groupid }}" class="text-xs text-indigo-600 hover:text-indigo-800">
                                    View all
                                </a>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach($groupedArticles[$group->groupid]->sortBy('article_order') as $article)
                                    <a href="{{ route('kb.article', $article->slug) }}" class="block px-4 md:px-6 py-3 md:py-4 hover:bg-gray-50 transition">
                                        <h3 class="font-medium text-gray-900 mb-1 text-sm md:text-base">{{ $article->subject }}</h3>
                                        <p class="text-gray-600 text-xs md:text-sm line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($article->description), 150) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if(isset($groupedArticles[null]) && $groupedArticles[null]->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-4 md:px-6 py-3 flex items-center gap-3 border-b border-gray-200">
                            <div class="w-6 md:w-8 h-6 md:h-8 rounded-lg flex items-center justify-center text-white bg-gray-400">
                                <i class="bi bi-folder text-xs md:text-sm"></i>
                            </div>
                            <h2 class="font-semibold text-gray-800 text-sm md:text-base">Ungrouped Articles</h2>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($groupedArticles[null]->sortBy('article_order') as $article)
                                <a href="{{ route('kb.article', $article->slug) }}" class="block px-4 md:px-6 py-3 md:py-4 hover:bg-gray-50 transition">
                                    <h3 class="font-medium text-gray-900 mb-1 text-sm md:text-base">{{ $article->subject }}</h3>
                                    <p class="text-gray-600 text-xs md:text-sm line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($article->description), 150) }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm p-6 md:p-12 text-center">
                <div class="w-12 md:w-16 h-12 md:h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-search text-xl md:text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">No articles found</h3>
                <p class="text-gray-600 text-sm mb-6">
                    @if(request('q') || request('group'))
                        Try searching with different keywords or categories.
                    @else
                        No articles available at the moment.
                    @endif
                </p>
                <a href="{{ route('kb') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    View All Articles
                </a>
            </div>
        @endif
    </div>
</div>
@endsection