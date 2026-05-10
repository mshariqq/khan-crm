<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CRM System') | {{ $global_settings['companyname'] ?? 'CRM' }}</title>
    @if(!empty($global_settings['favicon']))
    <link rel="icon" type="image/x-icon" href="{{ $global_settings['favicon'] }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary-color: {{ $global_settings['primary_color'] ?? '#2563eb' }};
            --secondary-color: {{ $global_settings['secondary_color'] ?? '#4f46e5' }};
        }
        .bg-primary { background-color: var(--primary-color) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        .hover\:bg-primary-dark:hover { filter: brightness(0.9); }
    </style>

    @if(($global_settings['enable_custom_css'] ?? '0') == '1' && !empty($global_settings['custom_css']))
    <style>
        {!! $global_settings['custom_css'] !!}
    </style>
    @endif

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    @yield('content')

    @stack('scripts')
</body>
</html>