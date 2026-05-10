@extends('layouts.admin')

@section('breadcrumb', 'Add Contact')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Contact</h1>
            <p class="text-slate-500 text-sm">Link a new contact person to a client company.</p>
        </div>
        <a href="{{ route('admin.contacts.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.contacts.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Personal Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="firstname" value="{{ old('firstname') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('firstname') border-red-500 @enderror" placeholder="John" required>
                        @error('firstname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="lastname" value="{{ old('lastname') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('lastname') border-red-500 @enderror" placeholder="Doe" required>
                        @error('lastname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('email') border-red-500 @enderror" placeholder="john.doe@example.com" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Phone Number</label>
                        <input type="text" name="phonenumber" value="{{ old('phonenumber') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="+1 234 567 890">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Job Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="CEO, Manager">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('password') border-red-500 @enderror" placeholder="••••••••" required>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Company Linkage</h3>
                
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-600">Client Company <span class="text-red-500">*</span></label>
                    <select name="userid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('userid') border-red-500 @enderror" required>
                        <option value="">Select Client Company</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->userid }}" {{ old('userid') == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                        @endforeach
                    </select>
                    @error('userid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_primary" id="is_primary" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('is_primary', true) ? 'checked' : '' }}>
                        <label for="is_primary" class="text-sm font-medium text-slate-600 cursor-pointer">Set as Primary Contact</label>
                    </div>
                    @error('is_primary') <p class="text-red-500 text-xs ml-7">{{ $message }}</p> @enderror
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="active" id="active" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('active', true) ? 'checked' : '' }}>
                        <label for="active" class="text-sm font-medium text-slate-600 cursor-pointer">Active Contact</label>
                    </div>
                    @error('active') <p class="text-red-500 text-xs ml-7">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.contacts.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Contact</button>
        </div>
    </form>
</div>
@endsection
