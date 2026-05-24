<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('taxes_view')) {
            abort(403, 'Unauthorized action.');
        }

        $taxes = Tax::paginate(15);
        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        if (!Auth::user()->can('taxes_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('taxes_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'taxrate' => 'required|numeric',
        ]);

        $tax = Tax::create($request->only(['name', 'taxrate']));
        app(AuditService::class)->log('tax_created', 'Tax created: ' . $tax->name, 'Tax', $tax->id, null, $tax->toArray());

        return redirect()->route('admin.taxes.index')->with('success', 'Tax created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('taxes_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $tax = Tax::findOrFail($id);
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('taxes_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'taxrate' => 'required|numeric',
        ]);

        $tax = Tax::findOrFail($id);
        $old = $tax->toArray();
        $tax->update($request->only(['name', 'taxrate']));
        app(AuditService::class)->log('tax_updated', 'Tax updated: ' . $tax->name, 'Tax', $tax->id, $old, $tax->fresh()->toArray());

        return redirect()->route('admin.taxes.index')->with('success', 'Tax updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('taxes_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $tax = Tax::findOrFail($id);
        app(AuditService::class)->log('tax_deleted', 'Tax deleted: ' . $tax->name, 'Tax', $tax->id, $tax->toArray());
        $tax->delete();

        return redirect()->route('admin.taxes.index')->with('success', 'Tax deleted successfully!');
    }
}
