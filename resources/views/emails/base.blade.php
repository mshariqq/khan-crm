<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f8fafc; padding-bottom: 40px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; margin-top: 40px; }
        .header { background-color: #0f172a; padding: 32px; text-align: center; }
        .logo { height: 40px; width: auto; }
        .content { padding: 40px; color: #334155; line-height: 1.6; font-size: 16px; }
        .footer { padding: 32px; text-align: center; color: #94a3b8; font-size: 12px; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #2563eb; color: #ffffff !important; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 24px; }
        .signature { margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9; font-style: italic; color: #64748b; }
        h1, h2, h3 { color: #1e293b; margin-top: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main">
            <div class="header">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ $company_name }}" class="logo">
                @else
                    <h2 style="color: #ffffff; margin: 0;">{{ $company_name }}</h2>
                @endif
            </div>
            <div class="content">
                {!! $body !!}
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} {{ $company_name }}. All rights reserved.<br>
                {!! $company_address !!}
            </div>
        </div>
    </div>
</body>
</html>
