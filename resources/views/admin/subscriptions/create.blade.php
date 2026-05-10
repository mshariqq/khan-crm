@extends('layouts.admin')

@section('breadcrumb', 'Add Subscription')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Subscription</h1>
            <p class="text-slate-500 text-sm">Register a new recurring subscription for a client.</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.subscriptions.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Subscription Details</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Subscription Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('name') border-red-500 @enderror" placeholder="Monthly Maintenance" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Client <span class="text-red-500">*</span></label>
                        <select name="clientid" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('clientid') border-red-500 @enderror" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->userid }}" {{ old('clientid') == $client->userid ? 'selected' : '' }}>{{ $client->company }}</option>
                            @endforeach
                        </select>
                        @error('clientid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Project</label>
                        <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('project_id') border-red-500 @enderror">
                            <option value="">No Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('description') border-red-500 @enderror" placeholder="Describe the subscription...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Terms</label>
                        <textarea name="terms" rows="3" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('terms') border-red-500 @enderror" placeholder="Payment terms...">{{ old('terms') }}</textarea>
                        @error('terms') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Billing & Status</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Currency <span class="text-red-500">*</span></label>
                            <select name="currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('currency') border-red-500 @enderror" required>
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ old('currency') == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol ?? '' }})</option>
                                @endforeach
                            </select>
                            @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Status</label>
                            <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('status') border-red-500 @enderror">
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Start Date <span class="text-red-500">*</span></label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('date') border-red-500 @enderror" required>
                            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Quantity</label>
                            <input type="number" step="0.01" name="quantity" value="{{ old('quantity', 1) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('quantity') border-red-500 @enderror" placeholder="1">
                            @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Stripe</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Stripe Plan ID</label>
                        <input type="text" name="stripe_plan_id" value="{{ old('stripe_plan_id') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('stripe_plan_id') border-red-500 @enderror" placeholder="plan_xxxxx">
                        @error('stripe_plan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Stripe Subscription ID</label>
                        <input type="text" name="stripe_subscription_id" value="{{ old('stripe_subscription_id') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('stripe_subscription_id') border-red-500 @enderror" placeholder="sub_xxxxx">
                        @error('stripe_subscription_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.subscriptions.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Subscription</button>
        </div>
    </form>
</div>
@endSection