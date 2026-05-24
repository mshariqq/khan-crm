<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Add New Item Group</h1>
        <p class="text-slate-500 text-sm">Create a new category for your items.</p>
    </div>
</div>

<form action="{{ route('admin.itemsgroups.store') }}" method="POST" data-pane-submit="1" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @csrf
    <div class="p-6 space-y-6">
        <div class="space-y-1">
            <label class="text-sm font-medium text-slate-600">Group Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Software Services" required>
        </div>
    </div>

    <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
        <button type="button" onclick="parent.closeTaskPane()" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Group</button>
    </div>
</form>
