@extends('layouts.admin')

@section('breadcrumb', 'Client Contacts')

@section('main')
<div class="p-6 space-y-6">
    @include('partials.admin.clients.profile_header')
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="space-y-4">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Contact Name</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Email</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Phone</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600">Status</th>
                        <th class="px-4 py-3 text-sm font-semibold text-slate-600 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($data as $contact)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">
                                {{ $contact->firstname }} {{ $contact->lastname }}
                                @if($contact->is_primary)
                                    <span class="ml-2 px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase">Primary</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $contact->email }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $contact->phonenumber ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 {{ $contact->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full text-xs font-bold">
                                    {{ $contact->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.contacts.edit', $contact->id) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400 text-sm">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="bi bi-person-lines-fill text-3xl"></i>
                                    <p>No contacts found yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                </table>
                <div class="mt-4">
                    {{ $data->appends(['tab' => 'contacts'])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
