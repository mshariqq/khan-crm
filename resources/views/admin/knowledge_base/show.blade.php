@extends('layouts.admin')

@section('title', 'View Knowledge Base Article')

@section('main')
<div class="max-w-3xl mx-auto py-6">
  <h2 class="text-2xl font-semibold mb-4">{{ $article->subject }}</h2>
  <div class="prose">
    {!! nl2br(e($article->description)) !!}
  </div>
  <p class="mt-4"><strong>Group:</strong> {{ optional($article->group)->name ?? '-' }}</p>
  <p><strong>Active:</strong> {{ $article->active ? 'Yes' : 'No' }}</p>
  <p><strong>Order:</strong> {{ $article->article_order }}</p>
  <div class="mt-4">
    <a href="{{ route('admin.knowledgebase.edit', $article->articleid) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Edit</a>
    <a href="{{ route('admin.knowledgebase.index') }}" class="ml-2 text-gray-600 hover:underline">Back to list</a>
  </div>
</div>
@endsection