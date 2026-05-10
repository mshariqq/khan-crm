@extends('layouts.guest')

@section('title', $article->subject)

@section('main')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-6 md:py-8">
        <a href="{{ route('kb') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 mb-4 text-sm font-medium transition">
            <i class="bi bi-arrow-left"></i> Back to Knowledge Base
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <article class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6 lg:p-8">
                    @if($article->articlegroup && $article->group)
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $article->group->color ?? '#6366f1' }}"></span>
                            <span class="text-xs font-medium text-gray-500">{{ $article->group->name }}</span>
                        </div>
                    @endif
                    
                    <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 md:mb-6">{{ $article->subject }}</h1>
                    
                    <div class="prose prose-sm md:prose max-w-none text-gray-600 leading-relaxed">
                        {!! nl2br(e($article->description)) !!}
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 mb-2">Was this article helpful?</p>
                                <form action="{{ route('kb.feedback', $article->articleid) }}" method="POST" class="inline-flex gap-2">
                                    @csrf
                                    <button type="submit" name="helpful" value="1" class="flex items-center gap-1.5 px-3 py-1.5 md:px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition">
                                        <i class="bi bi-hand-thumbs-up"></i> Yes
                                    </button>
                                    <button type="submit" name="helpful" value="0" class="flex items-center gap-1.5 px-3 py-1.5 md:px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-red-50 hover:border-red-300 hover:text-red-700 transition">
                                        <i class="bi bi-hand-thumbs-down"></i> No
                                    </button>
                                </form>
                            </div>
                            @if($feedbackCounts && ($feedbackCounts->likes > 0 || $feedbackCounts->dislikes > 0))
                                <div class="text-right text-sm text-gray-500">
                                    <span class="text-green-600 font-medium">{{ $feedbackCounts->likes ?? 0 }} found this helpful</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </article>

            <aside class="lg:col-span-1 space-y-4">
                @if($article->articlegroup)
                    @php
                        $relatedArticles = \App\Models\KnowledgeBase::where('articlegroup', $article->articlegroup)
                            ->where('articleid', '!=', $article->articleid)
                            ->where('active', 1)
                            ->orderBy('article_order')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($relatedArticles->count() > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="bi bi-collection text-indigo-600"></i> Related Articles
                            </h3>
                            <div class="space-y-2">
                                @foreach($relatedArticles as $related)
                                    <a href="{{ route('kb.article', $related->slug) }}" class="block py-2 text-sm text-gray-600 hover:text-indigo-600 transition border-l-2 border-transparent hover:border-indigo-500 pl-2 -ml-2">
                                        {{ $related->subject }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                @php
                    $allGroups = \App\Models\KnowledgeBaseGroup::where('active', 1)->orderBy('group_order')->get();
                @endphp

                @if($allGroups->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="bi bi-folder-fill text-indigo-600"></i> Categories
                        </h3>
                        <div class="space-y-1">
                            @foreach($allGroups as $group)
                                <a href="{{ route('kb') }}?group={{ $group->groupid }}" class="flex items-center gap-2 py-1.5 text-sm text-gray-600 hover:text-indigo-600 transition">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $group->color ?? '#6366f1' }}"></span>
                                    {{ $group->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-indigo-600 rounded-xl shadow-sm p-4 text-center">
                    <div class="text-white mb-2">
                        <i class="bi bi-headset text-2xl"></i>
                    </div>
                    <h3 class="text-white font-semibold text-sm mb-1">Need more help?</h3>
                    <p class="text-indigo-100 text-xs mb-3">Our support team is ready to assist you.</p>
                    <a href="/login/client" class="inline-block w-full py-2 bg-white text-indigo-600 text-sm font-semibold rounded-lg hover:bg-gray-100 transition">
                        Contact Support
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection