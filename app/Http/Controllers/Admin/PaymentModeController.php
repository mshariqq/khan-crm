<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMode;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentModeController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('paymentmodes_view')) {
            abort(403, 'Unauthorized action.');
        }

        $paymentModes = PaymentMode::paginate(15);
        return view('admin.paymentmodes.index', compact('paymentModes'));
    }

    public function create()
    {
        if (!Auth::user()->can('paymentmodes_create')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.paymentmodes.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('paymentmodes_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'show_on_pdf' => 'nullable|boolean',
            'invoices_only' => 'nullable|boolean',
            'expenses_only' => 'nullable|boolean',
            'selected_by_default' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ]);

        $paymentMode = PaymentMode::create($request->only(['name', 'description', 'show_on_pdf', 'invoices_only', 'expenses_only', 'selected_by_default', 'active']));
        app(AuditService::class)->log('payment_mode_created', 'Payment mode created: ' . $paymentMode->name, 'PaymentMode', $paymentMode->id, null, $paymentMode->toArray());

        return redirect()->route('admin.paymentmodes.index')->with('success', 'Payment Mode created successfully!');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('paymentmodes_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $paymentMode = PaymentMode::findOrFail($id);
        return view('admin.paymentmodes.edit', compact('paymentMode'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('paymentmodes_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'show_on_pdf' => 'nullable|boolean',
            'invoices_only' => 'nullable|boolean',
            'expenses_only' => 'nullable|boolean',
            'selected_by_default' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ]);

        $paymentMode = PaymentMode::findOrFail($id);
        $old = $paymentMode->toArray();
        $paymentMode->update($request->only(['name', 'description', 'show_on_pdf', 'invoices_only', 'expenses_only', 'selected_by_default', 'active']));
        app(AuditService::class)->log('payment_mode_updated', 'Payment mode updated: ' . $paymentMode->name, 'PaymentMode', $paymentMode->id, $old, $paymentMode->fresh()->toArray());

        return redirect()->route('admin.paymentmodes.index')->with('success', 'Payment Mode updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('paymentmodes_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $paymentMode = PaymentMode::findOrFail($id);
        app(AuditService::class)->log('payment_mode_deleted', 'Payment mode deleted: ' . $paymentMode->name, 'PaymentMode', $paymentMode->id, $paymentMode->toArray());
        $paymentMode->delete();

        return redirect()->route('admin.paymentmodes.index')->with('success', 'Payment Mode deleted successfully!');
    }
}
