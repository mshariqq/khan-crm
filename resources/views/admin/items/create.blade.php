@extends('layouts.admin')

@section('breadcrumb', 'Add Item')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Item</h1>
            <p class="text-slate-500 text-sm">Define a new product or service in your system.</p>
        </div>
        <a href="{{ route('admin.items.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.items.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Information</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Description <span class="text-red-500">*</span></label>
                        <input type="text" name="description" value="{{ old('description') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('description') border-red-500 @enderror" placeholder="e.g. Web Design Service" required>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                         <label class="text-sm font-medium text-slate-600">Long Description</label>
                         <textarea name="long_description" rows="4" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('long_description') border-red-500 @enderror">{{ old('long_description') }}</textarea>
                         @error('long_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    </div>

                     <div class="space-y-1">
                          <label class="text-sm font-medium text-slate-600">Item Group</label>
                          <div class="flex gap-2">
                              <select name="group_id" class="flex-1 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('group_id') border-red-500 @enderror">
                                  <option value="">No Group</option>
                                  @foreach($groups as $group)
                                      <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                                  @endforeach
                              </select>
                              <button type="button" onclick="openItemsGroupPane('{{ route('admin.itemsgroups.pane.create') }}')" class="px-3 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors" title="Add New Group">
                                  <i class="bi bi-plus-lg"></i>
                              </button>
                          </div>
                          @error('group_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                     </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Pricing & Taxation</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Rate <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="rate" value="{{ old('rate') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('rate') border-red-500 @enderror" placeholder="0.00" required>
                            @error('rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                             <label class="text-sm font-medium text-slate-600">Unit</label>
                             <input type="text" name="unit" value="{{ old('unit') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('unit') border-red-500 @enderror" placeholder="Hour, Piece, etc.">
                             @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                         <div class="space-y-1">
                             <label class="text-sm font-medium text-slate-600">Tax %</label>
                             <input type="number" step="0.01" name="tax" value="{{ old('tax') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('tax') border-red-500 @enderror" placeholder="0.00">
                             @error('tax') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                         </div>
                         <div class="space-y-1">
                             <label class="text-sm font-medium text-slate-600">Tax 2 %</label>
                             <input type="number" step="0.01" name="tax2" value="{{ old('tax2') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('tax2') border-red-500 @enderror" placeholder="0.00">
                             @error('tax2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                         </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.items.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Item</button>
        </div>
    </form>
</div>
@endsection
