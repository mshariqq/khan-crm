@extends('layouts.admin')

@section('breadcrumb', 'Client Contacts')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="bi bi-person-lines-fill text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Contacts</h2>
                    <p class="text-xs text-slate-500">{{ $data->total() }} records</p>
                </div>
            </div>
            <a href="{{ route('admin.contacts.create', ['client_id' => $client->userid]) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="bi bi-plus-lg"></i> Add Contact
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $contact)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-sm font-bold text-slate-600">
                                        {{ substr($contact->firstname, 0, 1) }}{{ substr($contact->lastname, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-slate-800">{{ $contact->firstname }} {{ $contact->lastname }}</span>
                                            @if($contact->is_primary)
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase">Primary</span>
                                            @endif
                                        </div>
                                        @if($contact->title)
                                            <p class="text-xs text-slate-500">{{ $contact->title }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $contact->email }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $contact->phonenumber ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $contact->active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $contact->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.contacts.edit', $contact->id) }}" class="p-2 text-slate-400 hover:text-amber-600 transition-colors" title="Edit Contact">
                                    <i class="bi bi-pencil-square text-lg"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-person-lines-fill text-4xl block mb-2"></i>
                                <p class="text-sm font-medium">No contacts found</p>
                                <p class="text-xs mt-1">Add a contact to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($data->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $data->appends(['tab' => 'contacts'])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
