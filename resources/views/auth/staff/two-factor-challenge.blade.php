@extends('layouts.guest')

@section('title', 'Two-Factor Authentication')

@section('main')
<div class="min-h-[calc(100vh-140px)] flex items-center justify-center px-4 bg-cover bg-center" style="background-image: url('{{ $global_settings['login_background_url'] ?? '' }}')">
    <div class="max-w-md w-full bg-white/95 backdrop-blur-sm p-8 rounded-2xl shadow-2xl border border-gray-100">
        <div class="text-center mb-8">
            @if(!empty($global_settings['company_logo']))
                <img src="{{ $global_settings['company_logo'] }}" alt="{{ $global_settings['companyname'] }}" class="h-12 w-auto mx-auto mb-4">
            @endif
            <h2 class="text-3xl font-extrabold text-gray-900">Two-Factor Auth</h2>
            <p class="text-gray-500 mt-2">Enter the verification code sent to <strong>{{ $staff->email }}</strong></p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('staff.2fa.verify') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Verification Code</label>
                <input type="text" name="code" required maxlength="6" autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]*"
                    class="w-full px-4 py-3 text-center text-2xl tracking-[8px] border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition outline-none"
                    placeholder="000000">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2.5 rounded-lg font-bold hover:bg-primary-dark transition shadow-md active:scale-95 transform">
                Verify & Login
            </button>
        </form>

        <form action="{{ route('staff.2fa.resend') }}" method="POST" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                Resend verification code
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login.staff') }}" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                &larr; Back to login
            </a>
        </div>
    </div>
</div>
@endsection
