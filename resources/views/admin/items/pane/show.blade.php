<div class="flex items-start justify-between mb-6">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-blue-200 flex-shrink-0">
            {{ strtoupper(substr($item->description, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-xl font-bold text-slate-800">{{ $item->description }}</h1>
            <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                <span>#{{ $item->id }}</span>
                @if($item->unit)
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span>{{ $item->unit }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.items.edit', $item->id) }}" data-pane="edit" data-pane-type="item" data-pane-id="{{ $item->id }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 border-b pb-2 mb-4">Description</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
            {{ $item->long_description ?? 'No detailed description provided.' }}
        </p>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 border-b pb-2 mb-4">Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Item Group</span>
                    <span class="text-slate-800 font-medium">{{ $item->group->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Unit</span>
                    <span class="text-slate-800 font-medium">{{ $item->unit ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 border-b pb-2 mb-4">Pricing & Taxes</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Rate</span>
                    <span class="text-slate-800 font-bold text-lg">{{ number_format($item->rate, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tax 1</span>
                    <span class="text-slate-800 font-medium">{{ $item->tax ?? '0' }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tax 2</span>
                    <span class="text-slate-800 font-medium">{{ $item->tax2 ?? '0' }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>
