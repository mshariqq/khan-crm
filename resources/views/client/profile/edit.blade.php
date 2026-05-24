@extends('layouts.client')

@section('title', 'Edit Profile')
@section('breadcrumb', 'Edit Profile')

@section('main')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Edit Profile</h1>
        <p class="text-sm text-slate-500">Update your personal information and password</p>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3">
        <i class="bi bi-check-circle-fill"></i>
        <span class="text-sm font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl">
        <div class="flex items-center gap-3 mb-2">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span class="text-sm font-semibold">Please fix the following errors:</span>
        </div>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('client.profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="firstname" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">First Name</label>
                        <input type="text" name="firstname" id="firstname" value="{{ old('firstname', Auth::guard('client')->user()->firstname) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('firstname') border-red-300 @enderror" required>
                        @error('firstname')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="lastname" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Last Name</label>
                        <input type="text" name="lastname" id="lastname" value="{{ old('lastname', Auth::guard('client')->user()->lastname) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('lastname') border-red-300 @enderror" required>
                        @error('lastname')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="phonenumber" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Phone Number</label>
                    <input type="text" name="phonenumber" id="phonenumber" value="{{ old('phonenumber', Auth::guard('client')->user()->phonenumber) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('phonenumber') border-red-300 @enderror">
                    @error('phonenumber')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="title" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Job Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', Auth::guard('client')->user()->title) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('title') border-red-300 @enderror">
                    @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-slate-200">

                <div>
                    <p class="text-sm font-bold text-slate-800 mb-1">Change Password</p>
                    <p class="text-xs text-slate-400 mb-4">Leave blank to keep current password</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">New Password</label>
                        <input type="password" name="password" id="password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('password') border-red-300 @enderror">
                        @error('password')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                </div>

                <hr class="border-slate-200">

                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Current Password <span class="text-red-500">*</span></label>
                    <input type="password" name="current_password" id="current_password" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all @error('current_password') border-red-300 @enderror" required>
                    <p class="text-xs text-slate-400 mt-1">Required to save any changes</p>
                    @error('current_password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                        <i class="bi bi-check-lg"></i> Save Changes
                    </button>
                    <a href="{{ route('client.dashboard') }}" class="px-4 py-2.5 text-slate-500 text-sm font-bold hover:text-slate-700 transition-all">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
