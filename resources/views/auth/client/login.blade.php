@extends('layouts.guest')

@section('title', 'Client Login')

@section('main')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center px-4 bg-cover bg-center" style="background-image: url('{{ $global_settings['login_background_url'] ?? '' }}')">
    <div class="max-w-md w-full bg-white/95 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-gray-100">
        <div class="text-center mb-8">
            @if(!empty($global_settings['company_logo']))
                <img src="{{ $global_settings['company_logo'] }}" alt="{{ $global_settings['companyname'] }}" class="h-12 w-auto mx-auto mb-4">
            @endif
            <h2 class="text-3xl font-extrabold text-gray-900">Customer Portal</h2>
            <p class="text-gray-500 mt-2">Access your projects and invoices from {{ $global_settings['companyname'] ?? 'us' }}</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login/client" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition outline-none">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg font-bold hover:bg-primary-dark transition shadow-md active:scale-95 transform">
                Access My Portal
            </button>
        </form>
    </div>
</div>
@endsection
