@extends('layouts.admin')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Create New Permission</h1>
            <p class="text-slate-500 text-sm">Define a new permission for the system.</p>
        </div>
        <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('admin.permissions.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-700">Permission Name</label>
                <input type="text" name="name" required placeholder="e.g. View Clients" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-medium text-slate-700">Permission Slug</label>
                <input type="text" name="slug" required placeholder="e.g. clients_view" class="w-full px-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                <p class="text-xs text-slate-400">The slug is used in the code to check for this permission.</p>
            </div>

            <div class="pt-6 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20 flex items-center gap-2">
                    <i class="bi bi-save"></i> Create Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
