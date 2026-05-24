<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate #{{ $estimate->number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 0; margin: 0; background: white; }
            .print-container { border: none; shadow: none; width: 100%; max-width: 100%; margin: 0; padding: 20px; }
        }
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-10">
    <div class="no-print flex justify-center mb-6 gap-4">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
            <i class="bi bi-printer"></i> Print Estimate
        </button>
        <button onclick="window.history.back()" class="px-6 py-2 bg-white text-slate-600 border border-slate-200 rounded-lg font-semibold hover:bg-slate-50 transition">
            Back
        </button>
    </div>

    <div class="print-container max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-100">
        <div class="bg-slate-900 p-8 md:p-12 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                @if(!empty($global_settings['company_logo']))
                    <img src="{{ $global_settings['company_logo'] }}" alt="{{ $global_settings['companyname'] }}" class="h-12 w-auto mb-4 brightness-0 invert">
                @else
                    <div class="text-3xl font-black tracking-tighter mb-2">{{ $global_settings['companyname'] ?? 'CRM' }}</div>
                @endif
                <div class="text-slate-400 text-sm max-w-xs">
                    {!! nl2br(e($global_settings['companyaddress'] ?? '')) !!}
                    @if(!empty($global_settings['companyphone']))<p class="mt-1">T: {{ $global_settings['companyphone'] }}</p>@endif
                    @if(!empty($global_settings['company_email']))<p>E: {{ $global_settings['company_email'] }}</p>@endif
                </div>
            </div>
            <div class="text-left md:text-right">
                <h1 class="text-5xl font-black uppercase tracking-tight text-amber-400 mb-2">Estimate</h1>
                <div class="space-y-1">
                    <p class="text-slate-400 text-sm">Number: <span class="text-white font-mono font-bold ml-2">#{{ $estimate->prefix ?? '' }}{{ $estimate->number }}</span></p>
                    <p class="text-slate-400 text-sm">Date: <span class="text-white font-bold ml-2">{{ \Carbon\Carbon::parse($estimate->date)->format('d M, Y') }}</span></p>
                    <p class="text-slate-400 text-sm">Expiry Date: <span class="text-white font-bold ml-2">{{ $estimate->expirydate ? \Carbon\Carbon::parse($estimate->expirydate)->format('d M, Y') : 'N/A' }}</span></p>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12 grid grid-cols-1 md:grid-cols-2 gap-12 border-b border-slate-100">
            <div>
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4">Bill To</h3>
                <div class="text-slate-800">
                    <p class="text-xl font-bold">{{ optional($estimate->client)->company ?? 'N/A' }}</p>
                    <div class="text-slate-500 mt-2 space-y-1">
                        <p>{{ $estimate->billing_street ?? optional($estimate->client)->address }}</p>
                        <p>{{ $estimate->billing_city ?? '' }}, {{ $estimate->billing_state ?? '' }} {{ $estimate->billing_zip ?? '' }}</p>
                        @if(optional($estimate->client)->phonenumber)<p>T: {{ $estimate->client->phonenumber }}</p>@endif
                        @if(optional($estimate->client)->email)<p>E: {{ $estimate->client->email }}</p>@endif
                    </div>
                </div>
            </div>
            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 flex flex-col justify-center">
                <h3 class="text-xs font-black uppercase tracking-widest text-amber-600 mb-2">Total Amount</h3>
                <div class="text-4xl font-black text-amber-900">
                    {{ app_format_money($estimate->total, $estimate->currency) }}
                </div>
                @php
                    $statusMap = [
                        1 => ['label' => 'Draft', 'class' => 'bg-slate-400'],
                        2 => ['label' => 'Sent', 'class' => 'bg-blue-500'],
                        3 => ['label' => 'Accepted', 'class' => 'bg-emerald-500'],
                        4 => ['label' => 'Declined', 'class' => 'bg-red-500'],
                        5 => ['label' => 'Converted', 'class' => 'bg-purple-500'],
                    ];
                    $s = $statusMap[$estimate->status] ?? ['label' => 'Unknown', 'class' => 'bg-slate-400'];
                @endphp
                <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 {{ $s['class'] }} text-white text-xs font-bold rounded-full uppercase tracking-wider w-fit">
                    <i class="bi bi-tag-fill"></i> {{ $s['label'] }}
                </div>
            </div>
        </div>

        @if($estimate->include_shipping && ($estimate->shipping_street || optional($estimate->client)->shipping_street))
        <div class="px-8 md:px-12 py-6 bg-slate-50 border-b border-slate-100">
            <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Shipping Address</h3>
            <div class="text-sm text-slate-600">
                <p>{{ $estimate->shipping_street ?? '' }}</p>
                <p>{{ $estimate->shipping_city ?? '' }}, {{ $estimate->shipping_state ?? '' }} {{ $estimate->shipping_zip ?? '' }}</p>
            </div>
        </div>
        @endif

        <div class="p-0">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-8 py-5 text-xs font-black uppercase tracking-widest text-slate-400">Description</th>
                        <th class="px-8 py-5 text-xs font-black uppercase tracking-widest text-slate-400 text-center">Qty</th>
                        <th class="px-8 py-5 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Rate</th>
                        <th class="px-8 py-5 text-xs font-black uppercase tracking-widest text-slate-400 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($estimate->items as $item)
                    <tr>
                        <td class="px-8 py-6">
                            <p class="font-bold text-slate-800">{!! $item->description !!}</p>
                        </td>
                        <td class="px-8 py-6 text-center text-slate-600">{{ $item->qty }}</td>
                        <td class="px-8 py-6 text-right text-slate-600">{{ app_format_money($item->rate, $estimate->currency) }}</td>
                        <td class="px-8 py-6 text-right font-bold text-slate-800">{{ app_format_money($item->amount, $estimate->currency) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-8 md:p-12 bg-slate-50/50 flex flex-col md:flex-row justify-between gap-12">
            <div class="flex-1 space-y-8">
                @if($estimate->terms)
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Terms & Conditions</h3>
                    <div class="text-sm text-slate-500 leading-relaxed">{!! nl2br(e($estimate->terms)) !!}</div>
                </div>
                @endif
                @if($estimate->clientnote)
                <div>
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Client Note</h3>
                    <div class="text-sm text-slate-500 leading-relaxed">{!! nl2br(e($estimate->clientnote)) !!}</div>
                </div>
                @endif
            </div>
            <div class="w-full md:w-80 space-y-4">
                <div class="flex justify-between text-slate-500">
                    <span class="text-sm">Subtotal</span>
                    <span class="font-bold">{{ app_format_money($estimate->subtotal, $estimate->currency) }}</span>
                </div>
                @if($estimate->total_tax > 0)
                <div class="flex justify-between text-slate-500">
                    <span class="text-sm">Total Tax</span>
                    <span class="font-bold">{{ app_format_money($estimate->total_tax, $estimate->currency) }}</span>
                </div>
                @endif
                @if($estimate->discount_total > 0)
                <div class="flex justify-between text-emerald-600 font-medium">
                    <span class="text-sm">Discount</span>
                    <span>-{{ app_format_money($estimate->discount_total, $estimate->currency) }}</span>
                </div>
                @endif
                @if($estimate->adjustment != 0)
                <div class="flex justify-between text-slate-500">
                    <span class="text-sm">Adjustment</span>
                    <span class="font-bold">{{ $estimate->adjustment > 0 ? '+' : '' }}{{ app_format_money($estimate->adjustment, $estimate->currency) }}</span>
                </div>
                @endif
                <div class="pt-4 border-t border-slate-200 flex justify-between items-center">
                    <span class="text-lg font-black text-slate-900">Total</span>
                    <span class="text-3xl font-black text-amber-600">{{ app_format_money($estimate->total, $estimate->currency) }}</span>
                </div>
            </div>
        </div>

        <div class="px-8 py-10 md:px-12 bg-white text-center border-t border-slate-100">
            <p class="text-slate-400 text-sm italic">Thank you for considering our proposal!</p>
            <div class="mt-4 flex justify-center gap-6 text-xs text-slate-400 font-bold uppercase tracking-widest">
                @if(!empty($global_settings['company_vat']))<span>VAT: {{ $global_settings['company_vat'] }}</span>@endif
                @if(!empty($global_settings['main_domain']))<span>{{ $global_settings['main_domain'] }}</span>@endif
            </div>
        </div>
    </div>
    
    <div class="mt-10 text-center text-slate-400 text-xs uppercase tracking-widest no-print">
        Generated on {{ date('d M, Y H:i') }}
    </div>
</body>
</html>