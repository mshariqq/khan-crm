@extends('layouts.admin')

@section('breadcrumb', 'Add Currency')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Currency</h1>
            <p class="text-slate-500 text-sm">Configure a new currency for your system.</p>
        </div>
        <a href="{{ route('admin.currencies.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.currencies.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Information</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Currency Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="US Dollar" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Symbol <span class="text-red-500">*</span></label>
                        <input type="text" name="symbol" value="{{ old('symbol') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('symbol') border-red-500 @enderror" placeholder="$" required>
                        @error('symbol') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Placement</label>
                        <select name="placement" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="left" {{ old('placement') == 'left' ? 'selected' : '' }}>Left (e.g. $100)</option>
                            <option value="right" {{ old('placement') == 'right' ? 'selected' : '' }}>Right (e.g. 100$)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Formatting Settings</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Decimal Separator</label>
                            <input type="text" name="decimal_separator" value="{{ old('decimal_separator', '.') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder=".">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Thousand Separator</label>
                            <input type="text" name="thousand_separator" value="{{ old('thousand_separator', ',') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder=",">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="isdefault" value="1" {{ old('isdefault') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label class="text-sm font-medium text-slate-600">Set as Default Currency</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.currencies.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Currency</button>
        </div>
    </form>
</div>
@endsection
