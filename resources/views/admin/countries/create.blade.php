@extends('layouts.admin')

@section('breadcrumb', 'Add Country')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Country</h1>
            <p class="text-slate-500 text-sm">Add a new country to the system.</p>
        </div>
        <a href="{{ route('admin.countries.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.countries.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Basic Information</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Long Name <span class="text-red-500">*</span></label>
                        <input type="text" name="long_name" value="{{ old('long_name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('long_name') border-red-500 @enderror" placeholder="United States" required>
                        @error('long_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Short Name <span class="text-red-500">*</span></label>
                        <input type="text" name="short_name" value="{{ old('short_name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('short_name') border-red-500 @enderror" placeholder="USA" required>
                        @error('short_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">ISO2 <span class="text-red-500">*</span></label>
                        <input type="text" name="iso2" value="{{ old('iso2') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('iso2') border-red-500 @enderror" placeholder="US" maxlength="2" required>
                        @error('iso2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Additional Details</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">ISO3</label>
                            <input type="text" name="iso3" value="{{ old('iso3') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="USA" maxlength="3">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Numcode</label>
                            <input type="text" name="numcode" value="{{ old('numcode') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="840">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Calling Code</label>
                            <input type="text" name="calling_code" value="{{ old('calling_code') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="+1">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">ccTLD</label>
                            <input type="text" name="cctld" value="{{ old('cctld') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder=".us">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-4">
                        <input type="checkbox" name="un_member" value="1" {{ old('un_member') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <label class="text-sm font-medium text-slate-600">UN Member</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.countries.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Country</button>
        </div>
    </form>
</div>
@endsection
