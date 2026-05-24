<div class="flex items-start justify-between mb-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-indigo-200 flex-shrink-0">
            {{ strtoupper(substr($group->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-800">{{ $group->name }}</h1>
            <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                <span>#{{ $group->id }}</span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.itemsgroups.edit', $group->id) }}" data-pane="edit" data-pane-type="itemsgroup" data-pane-id="{{ $group->id }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <h3 class="text-sm font-semibold text-slate-800 border-b pb-2 mb-4">Group Details</h3>
    <div class="space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="text-slate-500">Group Name</span>
            <span class="text-slate-800 font-medium">{{ $group->name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-slate-500">Items in Group</span>
            <span class="text-slate-800 font-medium">{{ $group->items_count }}</span>
        </div>
    </div>
</div>
