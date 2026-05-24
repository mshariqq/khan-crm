@extends('layouts.admin')

@section('breadcrumb', 'Email Templates')

@section('main')
<div class="p-4 space-y-4">
    <div class="flex items-center justify-between bg-white rounded-xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.settings.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                <i class="bi bi-envelope"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">Email Templates</h1>
                <p class="text-sm text-slate-500">Manage automated system notifications</p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap" id="emailTemplateTabs">
        @foreach($types as $t)
            <button type="button" data-type="{{ $t }}" class="email-type-chip px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $currentType == $t ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ ucfirst($t) }}
            </button>
        @endforeach
    </div>

    <div id="emailTemplatesContent">
        @include('admin.settings.email_templates_table')
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.getElementById('emailTemplateTabs');
    const content = document.getElementById('emailTemplatesContent');

    tabs.addEventListener('click', function(e) {
        const chip = e.target.closest('.email-type-chip');
        if (!chip) return;

        const type = chip.getAttribute('data-type');
        if (!type) return;

        tabs.querySelectorAll('.email-type-chip').forEach(c => {
            c.classList.remove('bg-slate-800', 'text-white');
            c.classList.add('bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
        });
        chip.classList.remove('bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
        chip.classList.add('bg-slate-800', 'text-white');

        content.innerHTML = '<div class="flex items-center justify-center py-12"><div class="text-slate-400 animate-pulse"><p class="text-sm font-medium">Loading...</p></div></div>';

        fetch('{{ route('admin.settings.email_templates.index') }}?type=' + type, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => {
            if (!r.ok) throw new Error('Failed to load');
            return r.text();
        })
        .then(html => {
            content.innerHTML = html;
        })
        .catch(() => {
            content.innerHTML = '<div class="flex items-center justify-center py-12 text-red-500"><p>Failed to load templates. Please try again.</p></div>';
        });
    });
});
</script>
@endsection