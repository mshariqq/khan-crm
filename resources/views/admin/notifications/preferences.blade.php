@extends('layouts.admin')

@section('breadcrumb', 'Notification Preferences')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                <i class="bi bi-bell"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Notification Preferences</h1>
                <p class="text-sm text-slate-500">Choose which notifications you receive and how</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 bg-green-100 text-green-700 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.notifications.preferences.update') }}" class="space-y-4">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-6 py-4 font-semibold text-slate-700">Notification Type</th>
                            <th class="text-center px-6 py-4 font-semibold text-slate-700">In-App</th>
                            <th class="text-center px-6 py-4 font-semibold text-slate-700">Email</th>
                            <th class="text-left px-6 py-4 font-semibold text-slate-700">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($allTypes as $key => $type)
                            @php
                                $inApp = $prefs[$key]->in_app ?? true;
                                $email = $prefs[$key]->email ?? true;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-medium text-slate-800">{{ $type['label'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="preferences[{{ $key }}][notification_type]" value="{{ $key }}">
                                        <input type="hidden" name="preferences[{{ $key }}][in_app]" value="0">
                                        <input type="checkbox" name="preferences[{{ $key }}][in_app]" value="1"
                                            class="sr-only peer" {{ $inApp ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="hidden" name="preferences[{{ $key }}][email]" value="0">
                                        <input type="checkbox" name="preferences[{{ $key }}][email]" value="1"
                                            class="sr-only peer" {{ $email ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs">{{ $type['desc'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <i class="bi bi-save"></i> Save Preferences
            </button>
        </div>
    </form>
</div>
@endsection
