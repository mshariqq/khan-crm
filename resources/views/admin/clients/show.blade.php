@extends('layouts.admin')

@section('breadcrumb', 'Client Profile')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    {{-- Overview Content --}}
    <div class="bg-white rounded-b-2xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Contact Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Phone</span>
                        <span class="text-slate-800 font-medium">{{ $client->phonenumber ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Website</span>
                        <span class="text-slate-800 font-medium">{{ $client->website ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">VAT</span>
                        <span class="text-slate-800 font-medium">{{ $client->vat ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Address</h3>
                <div class="space-y-3 text-sm">
                    <p class="text-slate-600 leading-relaxed">
                        {{ $client->address }}<br>
                        {{ $client->city }}, {{ $client->state }} {{ $client->zip }}<br>
                        {{ $client->country->name ?? 'N/A' }}
                    </p>
                </div>
            </div>
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Account Settings</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Currency</span>
                        <span class="text-slate-800 font-medium">{{ $client->currency->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Language</span>
                        <span class="text-slate-800 font-medium">{{ $client->default_language ?? 'Default' }}</span>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Quick Actions</h3>
                <a href="{{ route('admin.tasks.create') }}?rel_type=client&rel_id={{ $client->userid }}" data-pane="create" data-pane-type="task" class="block w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all text-center">
                    <i class="bi bi-plus-lg mr-1"></i> Create Task
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

