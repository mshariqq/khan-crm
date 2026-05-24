@extends('layouts.admin')

@section('breadcrumb', 'Email Preview')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                <i class="bi bi-eye"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Email Preview: {{ $template->name ?? '' }}</h1>
                <p class="text-sm text-slate-500">From: {{ $fromName }} &lt;{{ $fromEmail }}&gt;</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="bg-slate-900 px-6 py-3 text-white text-sm font-semibold flex items-center gap-2">
                    <i class="bi bi-envelope-fill"></i>
                    {{ $subject }}
                </div>
                <div class="p-0">
                    <iframe srcdoc="{{ $body }}" class="w-full border-0" style="min-height: 500px;" title="Email Preview"></iframe>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 border-b pb-3 mb-4">Preview Info</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-slate-500">Subject:</span>
                        <p class="font-medium text-slate-800 mt-1">{{ $subject }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">From:</span>
                        <p class="font-medium text-slate-800 mt-1">{{ $fromName }}</p>
                    </div>
                    <div>
                        <span class="text-slate-500">Reply-To:</span>
                        <p class="font-medium text-slate-800 mt-1">{{ $fromEmail }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-xl">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 border-b border-slate-800 pb-3 mb-4">Send Test Email</h3>
                <form id="testEmailForm" class="space-y-3">
                    @csrf
                    <input type="hidden" name="slug" value="{{ $template->slug ?? '' }}">
                    <div>
                        <label class="text-[10px] font-semibold text-slate-400 uppercase">Recipient Email</label>
                        <input type="email" name="to" value="{{ auth()->user()->email ?? '' }}" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none mt-1" required>
                    </div>
                    <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition text-sm flex items-center justify-center gap-2">
                        <i class="bi bi-send"></i> Send Test
                    </button>
                </form>
                <div id="testResult" class="mt-3 text-xs hidden"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('testEmailForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';
    var result = document.getElementById('testResult');
    result.className = 'mt-3 text-xs';

    fetch('{{ route("admin.settings.email_templates.test_send") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            slug: this.querySelector('[name="slug"]').value,
            to: this.querySelector('[name="to"]').value,
        }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            result.className = 'mt-3 text-xs text-green-400';
            result.textContent = data.message;
        } else {
            result.className = 'mt-3 text-xs text-red-400';
            result.textContent = data.message || 'Failed to send';
        }
    })
    .catch(function() {
        result.className = 'mt-3 text-xs text-red-400';
        result.textContent = 'Network error';
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send"></i> Send Test';
    });
});
</script>
@endsection
