<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('currencies_view')) {
            abort(403, 'Unauthorized action.');
        }

        $currencies = Currency::paginate(15);
        return view('admin.currencies.index', compact('currencies'));
    }

    public function create()
    {
        if (!Auth::user()->can('currencies_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.currencies.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('currencies_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'symbol' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'decimal_separator' => 'nullable|string|max:1',
            'thousand_separator' => 'nullable|string|max:1',
            'placement' => 'nullable|string|max:10',
            'isdefault' => 'nullable|boolean',
        ]);

        $currency = Currency::create($request->only(['symbol', 'name', 'decimal_separator', 'thousand_separator', 'placement', 'isdefault']));
        app(AuditService::class)->log('currency_created', 'Currency created: ' . $currency->name, 'Currency', $currency->id, null, $currency->toArray());

        return redirect()->route('admin.currencies.index')->with('success', 'Currency created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('currencies_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $currency = Currency::findOrFail($id);
        return view('admin.currencies.edit', compact('currency'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('currencies_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'symbol' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'decimal_separator' => 'nullable|string|max:1',
            'thousand_separator' => 'nullable|string|max:1',
            'placement' => 'nullable|string|max:10',
            'isdefault' => 'nullable|boolean',
        ]);

        $currency = Currency::findOrFail($id);
        $old = $currency->toArray();
        $currency->update($request->only(['symbol', 'name', 'decimal_separator', 'thousand_separator', 'placement', 'isdefault']));
        app(AuditService::class)->log('currency_updated', 'Currency updated: ' . $currency->name, 'Currency', $currency->id, $old, $currency->fresh()->toArray());

        return redirect()->route('admin.currencies.index')->with('success', 'Currency updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('currencies_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $currency = Currency::findOrFail($id);
        app(AuditService::class)->log('currency_deleted', 'Currency deleted: ' . $currency->name, 'Currency', $currency->id, $currency->toArray());
        $currency->delete();

        return redirect()->route('admin.currencies.index')->with('success', 'Currency deleted successfully!');
    }
}
