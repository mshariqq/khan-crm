<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
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

        Tax::create($request->all());

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
        $tax->update($request->all());

        return redirect()->route('admin.taxes.index')->with('success', 'Tax updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('taxes_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $tax = Tax::findOrFail($id);
        $tax->delete();

        return redirect()->route('admin.taxes.index')->with('success', 'Tax deleted successfully!');
    }
}
