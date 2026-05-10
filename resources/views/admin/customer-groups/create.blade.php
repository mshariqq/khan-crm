@extends('layouts.admin')

@section('breadcrumb', 'Add Client Group')

@section('main')
<div class="p-6">
    <div class="max-w-2xl bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h1 class="text-xl font-bold text-slate-800">Create New Group</h1>
        </div>
        <form action="{{ route('admin.customer-groups.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Group Name</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. VIP Clients" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.customer-groups.index') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                    Save Group
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
