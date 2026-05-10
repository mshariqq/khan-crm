@extends('layouts.guest')

@section('title', 'Welcome')

@section('main')
<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
        @if(!empty($global_settings['company_logo']))
            <img src="{{ $global_settings['company_logo'] }}" alt="{{ $global_settings['companyname'] }}" class="h-16 w-auto mx-auto mb-6">
        @else
            <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 text-3xl font-bold mx-auto mb-6">
                {{ substr($global_settings['companyname'] ?? 'C', 0, 1) }}
            </div>
        @endif
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome to {{ $global_settings['companyname'] ?? 'our CRM' }}</h1>
        <p class="text-gray-600 mb-2">Your partner in business efficiency and client management.</p>
        @if(!empty($global_settings['company_email']))
            <p class="text-gray-500 text-sm mb-8"><i class="bi bi-envelope mr-1"></i> {{ $global_settings['company_email'] }}</p>
        @else
            <div class="mb-8"></div>
        @endif
        
        <div class="space-y-3">
            <a href="/login/client" class="block w-full py-3 px-4 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-500/20">
                Client Portal Login
            </a>
            <a href="/login/staff" class="block w-full py-3 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                Staff Login
            </a>
        </div>
    </div>
    
    <div class="mt-8 text-center text-gray-400 text-sm">
        &copy; {{ date('Y') }} {{ $global_settings['companyname'] ?? 'CRM System' }}. All rights reserved.
    </div>
</div>
@endsection
