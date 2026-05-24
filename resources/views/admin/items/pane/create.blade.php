<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Add New Item</h1>
        <p class="text-slate-500 text-sm">Define a new product or service.</p>
    </div>
</div>

<form action="{{ route('admin.items.store') }}" method="POST" data-pane-submit="1" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    @csrf
    <div class="p-6 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Information</h3>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Description <span class="text-red-500">*</span></label>
                    <input type="text" name="description" value="{{ old('description') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="e.g. Web Design Service" required>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Long Description</label>
                    <textarea name="long_description" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Detailed description...">{{ old('long_description') }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Item Group</label>
                    <select name="group_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="">No Group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Pricing & Taxation</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Rate <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="rate" value="{{ old('rate') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Unit</label>
                        <input type="text" name="unit" value="{{ old('unit') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Hour, Piece, etc.">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Tax %</label>
                        <input type="number" step="0.01" name="tax" value="{{ old('tax') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Tax 2 %</label>
                        <input type="number" step="0.01" name="tax2" value="{{ old('tax2') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="0.00">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
        <button type="button" onclick="parent.closeTaskPane()" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</button>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Item</button>
    </div>
</form>
