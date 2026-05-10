@extends('layouts.client')

@section('title', 'Open New Ticket')
@section('breadcrumb', 'Open Ticket')

@section('main')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('client.tickets.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
            <i class="bi bi-arrow-left"></i> Back to Tickets
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h1 class="text-xl font-bold text-slate-800">Open New Support Ticket</h1>
            <p class="text-sm text-slate-500">Please provide as much detail as possible so we can help you.</p>
        </div>

        <form action="{{ route('client.tickets.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Department</label>
                    <select name="department" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->departmentid }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Priority</label>
                    <select name="priority" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" required>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->priorityid }}" {{ $priority->priorityid == 1 ? 'selected' : '' }}>{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Subject</label>
                <input type="text" name="subject" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Brief summary of the issue" required>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Message</label>
                <textarea name="message" rows="8" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Describe your issue in detail..." required></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
