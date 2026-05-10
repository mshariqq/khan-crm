@extends('layouts.client')

@section('title', 'Ticket: ' . $ticket->subject)
@section('breadcrumb', 'Ticket Details')

@section('main')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('client.tickets.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-blue-600 transition-colors">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ $ticket->subject }}</h1>
                <p class="text-sm text-slate-500">Ticket #{{ $ticket->ticketid }} · Opened {{ \Carbon\Carbon::parse($ticket->date)->diffForHumans() }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-blue-600">
                {{ $ticket->statusRel->name ?? 'Open' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ticket Conversation -->
        <div class="lg:col-span-2 space-y-6">
            <div class="space-y-6">
                <!-- Original Message -->
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-slate-800 flex items-center justify-center text-white font-bold shrink-0">
                        {{ strtoupper(substr($ticket->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                            <span class="text-xs font-bold text-slate-700">{{ $ticket->name }}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($ticket->date)->format('M d, Y · g:i A') }}</span>
                        </div>
                        <div class="p-6 text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{!! $ticket->message !!}</div>
                    </div>
                </div>

                <!-- Replies -->
                @foreach($ticket->replies as $reply)
                <div class="flex gap-4 {{ $reply->admin ? 'flex-row-reverse' : '' }}">
                    <div class="w-10 h-10 rounded-2xl {{ $reply->admin ? 'bg-blue-600' : 'bg-slate-800' }} flex items-center justify-center text-white font-bold shrink-0">
                        {{ strtoupper(substr($reply->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 bg-white rounded-2xl shadow-sm border {{ $reply->admin ? 'border-blue-100' : 'border-slate-200' }} overflow-hidden">
                        <div class="px-6 py-3 border-b {{ $reply->admin ? 'border-blue-50 bg-blue-50/30' : 'border-slate-50 bg-slate-50/30' }} flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-700">{{ $reply->name }}</span>
                                @if($reply->admin)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-blue-100 text-blue-700 uppercase tracking-tight">Staff</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ \Carbon\Carbon::parse($reply->date)->format('M d, Y · g:i A') }}</span>
                        </div>
                        <div class="p-6 text-sm text-slate-600 leading-relaxed whitespace-pre-wrap">{!! $reply->message !!}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Reply Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Post a Reply</h3>
                </div>
                <form action="{{ route('client.tickets.reply.store', $ticket->ticketid) }}" method="POST" class="p-6">
                    @csrf
                    <textarea name="message" rows="6" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all mb-4" placeholder="Write your reply here..." required></textarea>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                        Post Reply
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-6 uppercase tracking-wider text-center">Ticket Information</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Department</span>
                        <span class="font-bold text-slate-800">{{ $ticket->departmentRel->name ?? 'General' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Priority</span>
                        @php
                            $priorityColors = [
                                1 => 'text-emerald-600',
                                2 => 'text-amber-600',
                                3 => 'text-rose-600'
                            ];
                            $p = ($ticket->priorityRel && $ticket->priorityRel->priorityid) ? $ticket->priorityRel->priorityid : 1;
                        @endphp
                        <span class="font-bold {{ $priorityColors[$p] ?? 'text-slate-800' }}">
                            {{ $ticket->priorityRel->name ?? 'Low' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Last Reply</span>
                        <span class="font-bold text-slate-800">
                            {{ $ticket->lastreply ? \Carbon\Carbon::parse($ticket->lastreply)->diffForHumans() : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                <h3 class="text-sm font-bold text-blue-800 mb-2 uppercase tracking-wider">Support Hours</h3>
                <p class="text-xs text-blue-700 leading-relaxed">Our support team is available Monday through Friday, 9:00 AM to 6:00 PM EST. We usually respond within 24 hours.</p>
            </div>
        </div>
    </div>
</div>
@endsection
