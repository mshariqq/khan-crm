<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-800">Invoices ({{ $invoices->count() }})</h3>
    <a href="{{ route('admin.invoices.create') }}?project_id={{ $project->id }}" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 flex items-center gap-1">
        <i class="bi bi-plus"></i> Add Invoice
    </a>
</div>

@if($invoices->count() > 0)
<div class="space-y-2">
    @foreach($invoices as $invoice)
    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors group">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg {{ $invoice->status == 'paid' ? 'bg-emerald-100' : ($invoice->status == 'sent' ? 'bg-blue-100' : 'bg-slate-200') }} flex items-center justify-center">
                <i class="bi bi-receipt text-{{ $invoice->status == 'paid' ? 'emerald' : ($invoice->status == 'sent' ? 'blue' : 'slate') }}-600 text-sm"></i>
            </div>
            <div>
                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="text-sm font-medium text-slate-700 hover:text-blue-600">#{{ $invoice->number }}</a>
                <span class="text-[10px] text-slate-400 ml-2">{{ $invoice->date?->format('M j, Y') }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <span class="text-sm font-bold text-slate-700">{{ app_format_money($invoice->total, $invoice->currency) }}</span>
                <span class="text-[10px] block px-2 py-0.5 rounded-full mt-1
                    @if($invoice->status == 'paid') bg-emerald-100 text-emerald-700
                    @elseif($invoice->status == 'sent') bg-blue-100 text-blue-700
                    @else bg-slate-100 text-slate-600 @endif">
                    {{ ucfirst($invoice->status ?? 'Draft') }}
                </span>
            </div>
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="View">
                    <i class="bi bi-eye text-xs"></i>
                </a>
                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-slate-200" title="Edit">
                    <i class="bi bi-pencil text-xs"></i>
                </a>
                <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" class="inline" onsubmit="event.preventDefault(); confirmDelete('Confirm Delete', 'Delete this invoice?', this.action, (this.querySelector('[name=_method]')?.value || 'POST'));">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-slate-200" title="Delete">
                        <i class="bi bi-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <i class="bi bi-receipt text-slate-400 text-xl"></i>
    </div>
    <p class="text-sm text-slate-500">No invoices</p>
</div>
@endif