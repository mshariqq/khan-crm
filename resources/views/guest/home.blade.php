@extends('layouts.guest')

@section('title', 'Welcome to CRM')

@section('main')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 py-8 md:py-12">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-2">Welcome to {{ $global_settings['companyname'] ?? 'Our' }} Help Center</h1>
            <p class="text-gray-600 text-center mb-8 text-sm md:text-base">Search our knowledge base for answers to common questions</p>
            
            <form action="{{ route('kb') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="search" name="q" placeholder="Search for articles..." 
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 md:py-4 text-base md:text-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-24 md:pr-28">
                    <button type="submit" class="absolute right-1.5 top-1.5 md:right-2 md:top-2 bg-indigo-600 px-4 md:px-6 py-2 rounded-lg text-white font-medium hover:bg-indigo-700 transition text-sm md:text-base">
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-8 md:py-12">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Popular Articles</h2>
            <a href="{{ route('kb') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm md:text-base">View all →</a>
        </div>

        @php
            $groupedArticles = $articles->groupBy('articlegroup');
        @endphp

        <div class="grid gap-4 md:gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            @foreach(\App\Models\KnowledgeBaseGroup::where('active', 1)->orderBy('group_order')->get()->take(3) as $group)
                @if(isset($groupedArticles[$group->groupid]) && $groupedArticles[$group->groupid]->count() > 0)
                    @foreach($groupedArticles[$group->groupid]->sortBy('article_order')->take(2) as $article)
                        <a href="{{ route('kb.article', $article->slug) }}" class="bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition group">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $group->color ?? '#6366f1' }}"></span>
                                <span class="text-xs text-gray-500">{{ $group->name }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 text-sm md:text-base">{{ $article->subject }}</h3>
                            <p class="text-gray-600 text-xs md:text-sm line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($article->description), 100) }}</p>
                        </a>
                    @endforeach
                @endif
            @endforeach
            
            @if(isset($groupedArticles[null]) && $groupedArticles[null]->count() > 0)
                @foreach($groupedArticles[null]->sortBy('article_order')->take(3) as $article)
                    <a href="{{ route('kb.article', $article->slug) }}" class="bg-white rounded-xl shadow-sm p-4 md:p-5 hover:shadow-md transition group">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                            <span class="text-xs text-gray-500">General</span>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 text-sm md:text-base">{{ $article->subject }}</h3>
                        <p class="text-gray-600 text-xs md:text-sm line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($article->description), 100) }}</p>
                    </a>
                @endforeach
            @endif
        </div>

        @if($articles->count() === 0)
            <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 text-center">
                <div class="w-12 md:w-16 h-12 md:h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-book text-xl md:text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Articles Yet</h3>
                <p class="text-gray-600">Check back later for help articles.</p>
            </div>
        @endif

        <div class="mt-8 md:mt-12 bg-indigo-600 rounded-xl p-6 md:p-8 text-center">
            <h2 class="text-lg md:text-xl font-bold text-white mb-2">Can't find what you're looking for?</h2>
            <p class="text-indigo-100 mb-4 text-sm md:text-base">Contact our support team for personalized help.</p>
            <a href="/login/client" class="inline-block px-5 md:px-6 py-2 md:py-3 bg-white text-indigo-600 font-semibold rounded-lg hover:bg-gray-100 transition text-sm md:text-base">
                Contact Support
            </a>
        </div>
    </div>
</div>
@endsection