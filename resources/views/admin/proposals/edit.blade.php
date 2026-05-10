@extends('layouts.admin')

@section('breadcrumb', 'Edit Proposal')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    .ql-editor { font-size: 14px; padding: 15px; min-height: 200px; }
    .ql-toolbar { border: 1px solid #e2e8f0 !important; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background: #f8fafc; }
    .ql-container { border: 1px solid #e2e8f0 !important; border-top: none !important; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
</style>
@endpush

@section('main')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Proposal</h1>
            <p class="text-slate-500 text-sm">Update the details for {{ $proposal->subject }}.</p>
        </div>
        <a href="{{ route('admin.proposals.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition-all flex items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form action="{{ route('admin.proposals.update', $proposal->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">General Information</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" value="{{ old('subject', $proposal->subject) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all @error('subject') border-red-500 @enderror" required>
                        @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Proposal To <span class="text-red-500">*</span></label>
                        <input type="text" name="proposal_to" value="{{ old('proposal_to', $proposal->proposal_to) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                        @error('proposal_to') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Email</label>
                        <input type="email" name="email" value="{{ old('email', $proposal->email) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $proposal->phone) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Relate to Project/Lead</label>
                        <div class="flex gap-2">
                            <select name="rel_type" class="w-1/3 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="">Type</option>
                                <option value="project" {{ old('rel_type', $proposal->rel_type) == 'project' ? 'selected' : '' }}>Project</option>
                                <option value="lead" {{ old('rel_type', $proposal->rel_type) == 'lead' ? 'selected' : '' }}>Lead</option>
                            </select>
                            <select name="rel_id" class="w-2/3 px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="">Select</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('rel_id', $proposal->rel_id) == $project->id && old('rel_type', $proposal->rel_type) == 'project' ? 'selected' : '' }}>{{ $project->name }} (Project)</option>
                                @endforeach
                                @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" {{ old('rel_id', $proposal->rel_id) == $lead->id && old('rel_type', $proposal->rel_type) == 'lead' ? 'selected' : '' }}>{{ $lead->company ?? $lead->name }} (Lead)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Project</label>
                        <select name="project_id" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">No Project</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ old('project_id', $proposal->project_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Settings & Dates</h3>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Currency <span class="text-red-500">*</span></label>
                        <select name="currency" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('currency', $proposal->currency) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->symbol ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('currency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-600">Assigned Staff</label>
                        <select name="assigned" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            <option value="">Unassigned</option>
                            @foreach($staff as $s)
                                <option value="{{ $s->staffid }}" {{ old('assigned', $proposal->assigned) == $s->staffid ? 'selected' : '' }}>{{ $s->firstname }} {{ $s->lastname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Date <span class="text-red-500">*</span></label>
                            <input type="date" name="date" value="{{ old('date', $proposal->date) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" required>
                            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Open Till</label>
                            <input type="date" name="open_till" value="{{ old('open_till', $proposal->open_till) }}" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Status</label>
                            <select name="status" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="1" {{ old('status', $proposal->status) == '1' ? 'selected' : '' }}>Open</option>
                                <option value="2" {{ old('status', $proposal->status) == '2' ? 'selected' : '' }}>Accepted</option>
                                <option value="3" {{ old('status', $proposal->status) == '3' ? 'selected' : '' }}>Declined</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Show Qty As</label>
                            <select name="show_quantity_as" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="1" {{ old('show_quantity_as', $proposal->show_quantity_as) == '1' ? 'selected' : '' }}>Qty</option>
                                <option value="2" {{ old('show_quantity_as', $proposal->show_quantity_as) == '2' ? 'selected' : '' }}>Hours</option>
                                <option value="3" {{ old('show_quantity_as', $proposal->show_quantity_as) == '3' ? 'selected' : '' }}>Days</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', $proposal->allow_comments) ? 'checked' : '' }} class="rounded border-slate-300" id="allow_comments">
                        <label for="allow_comments" class="text-sm font-medium text-slate-600">Allow Comments</label>
                    </div>

                    <div class="space-y-4 pt-2">
                        <h4 class="text-sm font-semibold text-slate-600 uppercase">Address</h4>
                        <textarea name="address" rows="2" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="Street">{{ old('address', $proposal->address) }}</textarea>
                        <div class="grid grid-cols-3 gap-3">
                            <input type="text" name="city" value="{{ old('city', $proposal->city) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="City">
                            <input type="text" name="state" value="{{ old('state', $proposal->state) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="State">
                            <input type="text" name="zip" value="{{ old('zip', $proposal->zip) }}" class="px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="ZIP">
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-600">Country</label>
                            <select name="country" class="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                <option value="">Select Country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->country_id }}" {{ old('country', $proposal->country) == $c->country_id ? 'selected' : '' }}>{{ $c->name ?? $c->country_name ?? $c->short_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Content</h3>
                <div id="editor" class="mt-1">{!! old('content', $proposal->content) !!}</div>
                <input type="hidden" name="content" id="content-input">
                @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Description</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-20">Qty</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Rate</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-24">Tax</th>
                                <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-28 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @for($i = 0; $i < 5; $i++)
                            <tr>
                                <td class="px-4 py-2">
                                    <input type="text" name="items[{{ $i }}][description]" value="{{ old('items.' . $i . '.description') }}" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Item description">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[{{ $i }}][qty]" value="{{ old('items.' . $i . '.qty', 1) }}" step="0.01" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[{{ $i }}][rate]" value="{{ old('items.' . $i . '.rate') }}" step="0.01" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                                </td>
                                <td class="px-4 py-2">
                                    <select name="items[{{ $i }}][tax]" class="w-full px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="">None</option>
                                        @foreach($taxes as $tax)
                                            <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->taxrate }}%)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <span class="text-sm font-medium text-slate-700">0.00</span>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div></div>
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2 mb-4">Totals</h3>
                    <div class="space-y-3 bg-slate-50 p-4 rounded-xl">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Subtotal</span>
                            <input type="number" step="0.01" name="subtotal" value="{{ old('subtotal', $proposal->subtotal) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Tax Total</span>
                            <input type="number" step="0.01" name="total_tax" value="{{ old('total_tax', $proposal->total_tax) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Discount Type</span>
                            <select name="discount_type" class="w-36 px-3 py-1.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="">No Discount</option>
                                <option value="before_tax" {{ old('discount_type', $proposal->discount_type) == 'before_tax' ? 'selected' : '' }}>Before Tax</option>
                                <option value="after_tax" {{ old('discount_type', $proposal->discount_type) == 'after_tax' ? 'selected' : '' }}>After Tax</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Discount Percent</span>
                            <input type="number" step="0.01" name="discount_percent" value="{{ old('discount_percent', $proposal->discount_percent) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Discount Total</span>
                            <input type="number" step="0.01" name="discount_total" value="{{ old('discount_total', $proposal->discount_total) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Adjustment</span>
                            <input type="number" step="0.01" name="adjustment" value="{{ old('adjustment', $proposal->adjustment) }}" class="w-36 px-3 py-1.5 text-sm text-right border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                            <span class="text-base font-bold text-slate-800">Total</span>
                            <input type="number" step="0.01" name="total" value="{{ old('total', $proposal->total) }}" class="w-36 px-3 py-1.5 text-sm text-right font-bold border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
            <a href="{{ route('admin.proposals.index') }}" class="px-6 py-2 text-slate-600 font-semibold rounded-lg hover:bg-slate-200 transition-all">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">Update Proposal</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Proposal content...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'clean']
                ]
            }
        });

        const form = document.querySelector('form');
        const contentInput = document.querySelector('#content-input');

        form.onsubmit = function() {
            contentInput.value = quill.root.innerHTML;
        };
    });
</script>
@endpush