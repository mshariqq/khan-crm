@extends('layouts.admin')

@section('breadcrumb', 'Add Client')

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Add New Client</h1>
            <p class="text-slate-500 text-sm">Register a new company in your CRM system.</p>
        </div>
        <a href="{{ route('admin.clients.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.clients.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Company Details</h3>
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company" value="{{ old('company') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('company') border-red-500 @enderror" placeholder="Acme Corp" required>
                        @error('company') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">VAT Number</label>
                        <input type="text" name="vat" value="{{ old('vat') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="VAT123456789">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Phone Number</label>
                        <input type="text" name="phonenumber" value="{{ old('phonenumber') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="+1 234 567 890">
                    </div>

                     <div class="space-y-1">
                         <label class="text-sm font-medium text-slate-600">Website</label>
                         <input type="text" name="website" value="{{ old('website') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="https://example.com">
                     </div>
 
                     <div class="space-y-1">
                         <label class="text-sm font-medium text-slate-600">Client Group</label>
                         <select name="customer_group_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                             <option value="">No Group</option>
                             @foreach($groups as $group)
                                 <option value="{{ $group->id }}" {{ old('customer_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                             @endforeach
                         </select>
                     </div>

                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Regional Settings</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Country <span class="text-red-500">*</span></label>
                            <select name="country" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('country') border-red-500 @enderror" required>
                                <option value="">Select Country</option>
                                 @foreach($countries as $country)
                                     <option value="{{ $country->country_id }}" {{ old('country') == $country->country_id ? 'selected' : '' }}>{{ $country->long_name }}</option>
                                 @endforeach

                            </select>
                            @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Default Currency <span class="text-red-500">*</span></label>
                            <select name="default_currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('default_currency') border-red-500 @enderror" required>
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" {{ old('default_currency') == $currency->id ? 'selected' : '' }}>{{ $currency->name }}</option>
                                @endforeach
                            </select>
                            @error('default_currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">City</label>
                            <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="New York">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">State/Province</label>
                            <input type="text" name="state" value="{{ old('state') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="NY">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="123 Main St">
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.clients.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Save Client</button>
        </div>
    </form>
</div>
@endsection
