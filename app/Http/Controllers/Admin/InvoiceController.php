<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Staff;
use App\Models\Project;
use App\Models\Subscription;
use App\Models\Item;
use App\Models\Tax;
use App\Models\PaymentMode;
use App\Models\Note;
use App\Models\Reminder;
use App\Models\SalesActivity;
use App\Models\InvoicePaymentRecord;
use App\Models\Credit;
use App\Models\CreditNote;
use App\Services\EmailService;
use App\Services\InvoiceService;
use App\Services\InvoiceNotificationService;
use App\Services\AuditService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $emailService;
    protected $invoiceService;
    protected $invoiceNotification;
    protected $auditService;

    public function __construct(EmailService $emailService, InvoiceService $invoiceService, InvoiceNotificationService $invoiceNotification, AuditService $auditService)
    {
        $this->emailService = $emailService;
        $this->invoiceService = $invoiceService;
        $this->invoiceNotification = $invoiceNotification;
        $this->auditService = $auditService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('invoices_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Invoice::query()->with(['client', 'currency', 'creator']);

        if ($request->filled('client')) {
            $query->where('clientid', $request->client);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('number', 'like', '%' . $request->search . '%');
        }

        $total = Invoice::count();
        $paid = Invoice::where('status', Invoice::STATUS_PAID)->count();
        $unpaid = Invoice::where('status', Invoice::STATUS_UNPAID)->count();
        $overdue = Invoice::where('status', Invoice::STATUS_UNPAID)->where('duedate', '<', now())->count();

        $invoices = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $clients = Client::all();

        return view('admin.invoices.index', compact('invoices', 'total', 'paid', 'unpaid', 'overdue', 'clients'));
    }

    public function create()
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $clients = Client::all();
        $currencies = Currency::all();
        $staff = Staff::all();
        $projects = Project::all();
        $subscriptions = Subscription::all();
        $items = Item::all();
        $taxes = Tax::all();
        $paymentModes = PaymentMode::where('active', 1)->get();

        // Get next invoice number
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $next_invoice_number = $lastInvoice ? (int)preg_replace('/[^0-9]/', '', $lastInvoice->number) + 1 : (int)\App\Helpers\SettingsHelper::get('next_invoice_number', 1);

        // Calculate default due date
        $days = (int)\App\Helpers\SettingsHelper::get('invoice_due_after', 30);
        $default_due_date = now()->addDays($days)->format('Y-m-d');

        // Get default terms and notes
        $default_terms = \App\Helpers\SettingsHelper::get('invoice_terms', '');
        $default_client_note = \App\Helpers\SettingsHelper::get('invoice_client_note', '');

        return view('admin.invoices.create', compact('clients', 'currencies', 'staff', 'projects', 'subscriptions', 'items', 'taxes', 'paymentModes', 'next_invoice_number', 'default_due_date', 'default_terms', 'default_client_note'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $invoiceData = $this->invoiceService->prepareInvoiceData($validated, $request);
            $invoiceData['number'] = $this->invoiceService->generateNextNumber();

            $invoice = Invoice::create($invoiceData);

            $this->invoiceService->logActivity($invoice, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> created');

            $totals = $this->invoiceService->saveItems($invoice, $request);
            $this->invoiceService->recalculateTotals($invoice, $totals['subtotal'], $totals['total_tax'], (float) ($validated['adjustment'] ?? 0), (float) ($validated['discount_total'] ?? 0));

            if ($invoice->total >= ($request->high_value_threshold ?? 100000)) {
                $this->invoiceNotification->send('high-value-invoice-created', $invoice->id);
            }

            DB::commit();

            if ($request->filled('referer')) {
                return redirect($request->referer)->with('success', 'Invoice created successfully!');
            }

            return redirect()->route('admin.invoices.index')->with('success', 'Invoice created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()])->withInput();
        }
    }

    public function show($id)
    {
        if (!Auth::user()->can('invoices_view')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::with([
            'client', 'currency', 'creator', 'saleAgent', 'project', 'subscription',
            'items', 'payments', 'credits.creditNote', 'notes.creator', 'reminders.staff',
            'checkoutLogs', 'salesActivities.staff'
        ])->findOrFail($id);

        $staff = Staff::all();
        $paymentModes = PaymentMode::where('active', 1)->get();

        return view('admin.invoices.show', compact('invoice', 'staff', 'paymentModes'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::with('items')->findOrFail($id);
        $clients = Client::all();
        $currencies = Currency::all();
        $staff = Staff::all();
        $projects = Project::all();
        $subscriptions = Subscription::all();
        $items = Item::all();
        $taxes = Tax::all();
        $paymentModes = PaymentMode::where('active', 1)->get();

        return view('admin.invoices.edit', compact('invoice', 'clients', 'currencies', 'staff', 'projects', 'subscriptions', 'items', 'taxes', 'paymentModes'));
    }

    public function update(UpdateInvoiceRequest $request, $id)
    {
        $validated = $request->validated();

        $invoice = Invoice::findOrFail($id);

        if (in_array($invoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CANCELLED])) {
            return back()->withErrors(['error' => 'Cannot modify a paid or cancelled invoice.']);
        }

        $oldItems = $invoice->items()->get()->toArray();
        $oldData = $invoice->toArray();

        $validated['number'] = (int)preg_replace('/[^0-9]/', '', $validated['number']);
        $validated['include_shipping'] = $validated['include_shipping'] ?? 0;
        $validated['show_shipping_on_invoice'] = $validated['show_shipping_on_invoice'] ?? 0;
        $validated['cancel_overdue_reminders'] = $validated['cancel_overdue_reminders'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_total'] = $validated['discount_total'] ?? 0;
        $validated['adjustment'] = $validated['adjustment'] ?? 0;
        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['total_tax'] = $validated['total_tax'] ?? 0;
        $validated['sale_agent'] = $validated['sale_agent'] ?? 0;
        $validated['project_id'] = $validated['project_id'] ?? 0;
        $validated['subscription_id'] = $validated['subscription_id'] ?? 0;
        $validated['billing_country'] = $validated['billing_country'] ?? 0;
        $validated['shipping_country'] = $validated['shipping_country'] ?? 0;
        $showQtyMap = ['qty' => 1, 'hours' => 2, 'items' => 3];
        $validated['show_quantity_as'] = $showQtyMap[$validated['show_quantity_as']] ?? (int)($validated['show_quantity_as'] ?? 1);
        $validated['discount_type'] = $validated['discount_type'] ?? 'percent';
        $invoice->update($validated);

        $newItems = [];
        $totals = $this->invoiceService->syncItemsFromRequest($invoice, $request, $newItems);
        $this->invoiceService->recalculateTotals($invoice, $totals['subtotal'], $totals['total_tax'], (float) ($validated['adjustment'] ?? 0), (float) ($validated['discount_total'] ?? 0));

        $newData = $invoice->fresh()->toArray();
        $newData['items'] = $newItems;
        $oldData['items'] = $oldItems;

        $this->auditService->log(
            'invoice_updated',
            "Invoice #{$invoice->number} updated",
            'Invoice',
            $invoice->id,
            $oldData,
            $newData,
            null,
            $request
        );

        $this->invoiceService->logActivity($invoice, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> updated');

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice updated successfully!');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('invoices_delete')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::findOrFail($id);
        $invId = $invoice->id;
        $num = $invoice->number;
        $prefix = $invoice->prefix;
        $invoice->delete();

        $this->logActivity($invId, 'Invoice <b>#' . ($prefix ?? '') . $num . '</b> deleted');

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted successfully!');
    }

    public function storeNote(Request $request, $id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate(['description' => 'required|string']);

        $invoice = Invoice::findOrFail($id);
        Note::create([
            'description' => $request->description,
            'rel_id' => $invoice->id,
            'rel_type' => 'invoice',
            'dateadded' => now(),
            'addedfrom' => Auth::id(),
        ]);

        $this->logActivity($invoice->id, 'Note added to Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Note added!');
    }

    public function storePayment(Request $request, $id)
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'paymentmode' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($id);

        // State machine: cannot record payment on cancelled or fully paid invoices
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return back()->withErrors(['error' => 'Cannot record payment on a cancelled invoice.']);
        }

        $remaining = $invoice->total - $invoice->totalSettled();
        if ($request->amount > $remaining) {
            return back()->withErrors(['amount' => 'Payment amount (' . $request->amount . ') exceeds the remaining balance (' . $remaining . ').']);
        }

        $paymentMode = PaymentMode::find($request->paymentmode);

        try {
            DB::beginTransaction();

            InvoicePaymentRecord::create([
                'invoiceid' => $invoice->id,
                'amount' => $request->amount,
                'paymentmode' => $request->paymentmode,
                'date' => $request->date,
                'note' => $request->note,
                'addedfrom' => Auth::id(),
                'daterecorded' => now(),
            ]);

            $totalPaid = $invoice->payments()->sum('amount');
            $wasJustPaid = false;
            if ($totalPaid >= $invoice->total) {
                $invoice->update(['status' => Invoice::STATUS_PAID]);
                $wasJustPaid = true;
            } elseif ($totalPaid > 0) {
                $invoice->update(['status' => Invoice::STATUS_PARTIALLY_PAID]);
            }

            $paymentOptions = [
                'payment_total' => app_format_money($request->amount, $invoice->currency),
                'payment_date' => \Carbon\Carbon::parse($request->date)->format('d M, Y'),
                'payment_method' => $paymentMode->name ?? '',
            ];

            $this->invoiceNotification->send('invoice-payment-recorded', $invoice->id, $paymentOptions);
            $this->invoiceNotification->send('invoice-payment-received-staff', $invoice->id, $paymentOptions);

            if ($wasJustPaid) {
                $this->invoiceNotification->send('invoice-paid', $invoice->id, $paymentOptions);
            }

            $this->logActivity($invoice->id, 'Payment of <b>' . app_format_money($request->amount, $invoice->currency) . '</b> recorded for Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Payment recorded!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to record payment: ' . $e->getMessage()]);
        }
    }

    public function editPayment($invoiceId, $paymentId)
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::findOrFail($invoiceId);
        $payment = InvoicePaymentRecord::findOrFail($paymentId);
        $paymentModes = PaymentMode::where('active', 1)->get();

        return view('admin.invoices.payment_edit', compact('invoice', 'payment', 'paymentModes'));
    }

    public function updatePayment(Request $request, $invoiceId, $paymentId)
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'paymentmode' => 'required|integer',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'transactionid' => 'nullable|string',
        ]);

        $payment = InvoicePaymentRecord::findOrFail($paymentId);
        $payment->update([
            'amount' => $request->amount,
            'paymentmode' => $request->paymentmode,
            'date' => $request->date,
            'note' => $request->note,
            'transactionid' => $request->transactionid,
        ]);

        $invoice = Invoice::findOrFail($invoiceId);
        $totalPaid = $invoice->payments()->sum('amount');
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => Invoice::STATUS_PAID]);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => Invoice::STATUS_PARTIALLY_PAID]);
        } else {
            $invoice->update(['status' => Invoice::STATUS_UNPAID]);
        }

        $this->logActivity($invoice->id, 'Payment of <b>' . app_format_money($request->amount, $invoice->currency) . '</b> updated for Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Payment updated!');
    }

    public function destroyPayment($invoiceId, $paymentId)
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $payment = InvoicePaymentRecord::findOrFail($paymentId);
        $payment->delete();

        $invoice = Invoice::findOrFail($invoiceId);
        $totalPaid = $invoice->payments()->sum('amount');
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => Invoice::STATUS_PAID]);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => Invoice::STATUS_PARTIALLY_PAID]);
        } else {
            $invoice->update(['status' => Invoice::STATUS_UNPAID]);
        }

        $this->logActivity($invoice->id, 'Payment deleted for Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Payment deleted!');
    }

    public function storeCredit(Request $request, $id)
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'credit_id' => 'required|integer|exists:tblcreditnotes,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::with('currency')->findOrFail($id);
        $creditNote = CreditNote::with('currency')->findOrFail($request->credit_id);

        if ($request->amount > $creditNote->remainingCredit()) {
            return redirect()->back()->with('error', 'Amount exceeds remaining credit on this credit note.');
        }

        if ($creditNote->currency && $invoice->currency && $creditNote->currency->id !== $invoice->currency->id) {
            return redirect()->back()->with('error', 'Currency mismatch: The credit note is in ' . ($creditNote->currency->name ?? 'N/A') . ' but the invoice is in ' . ($invoice->currency->name ?? 'N/A') . '. Credits can only be applied within the same currency.');
        }

        Credit::create([
            'invoice_id' => $invoice->id,
            'credit_id' => $creditNote->id,
            'staff_id' => Auth::id(),
            'date' => now(),
            'date_applied' => now(),
            'amount' => $request->amount,
        ]);

        $this->logActivity($invoice->id, 'Credit of <b>' . app_format_money($request->amount, $invoice->currency) . '</b> applied to Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Credit applied!');
    }

    public function destroyCredit($invoiceId, $creditId)
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $credit = Credit::findOrFail($creditId);
        $credit->delete();

        $invoice = Invoice::findOrFail($invoiceId);
        $this->logActivity($invoice->id, 'Credit removed from Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Credit removed!');
    }

    public function markAsPaid($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::findOrFail($id);

        // State machine: cannot mark cancelled invoice as paid
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return back()->withErrors(['error' => 'Cannot mark a cancelled invoice as paid.']);
        }
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->withErrors(['error' => 'Invoice is already marked as paid.']);
        }

        $invoice->update(['status' => Invoice::STATUS_PAID]);

        $this->invoiceNotification->send('invoice-paid', $invoice->id);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> marked as paid');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice marked as paid!');
    }

    public function markAsCancelled($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::findOrFail($id);

        // State machine: cannot cancel a paid invoice
        if ($invoice->status === Invoice::STATUS_PAID) {
            return back()->withErrors(['error' => 'Cannot cancel a paid invoice.']);
        }
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            return back()->withErrors(['error' => 'Invoice is already cancelled.']);
        }

        $invoice->update(['status' => Invoice::STATUS_CANCELLED]);

        $this->invoiceNotification->send('invoice-cancelled', $invoice->id);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> cancelled');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice cancelled!');
    }

    public function sendInvoice($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::findOrFail($id);

        $this->invoiceNotification->send('invoice-send-to-client', $invoice->id);

        $invoice->update(['sent' => 1, 'datesend' => now()]);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> sent to client');

        return redirect()->back()->with('success', 'Invoice sent to client!');
    }

    public function sendNotification(Request $request, $id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['slug' => 'required|string']);

        $options = $request->except('slug');
        $result = $this->invoiceNotification->send($request->slug, $id, $options);

        return response()->json($result);
    }

    public function print($id)
    {
        if (!Auth::user()->can('invoices_view')) {
            abort(403, 'Unauthorized action.');
        }
        $invoice = Invoice::with(['client', 'currency', 'items'])->findOrFail($id);
        return view('admin.invoices.pdf', compact('invoice'));
    }

    public function storeReminder(Request $request, $id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'date' => 'required|date',
            'staff' => 'required|integer',
            'description' => 'required|string',
            'notify_by_email' => 'nullable|boolean'
        ]);

        $invoice = Invoice::findOrFail($id);

        $reminder = Reminder::create([
            'description' => $request->description,
            'date' => $request->date,
            'rel_id' => $id,
            'rel_type' => 'invoice',
            'staff' => $request->staff,
            'notify_by_email' => $request->has('notify_by_email') ? 1 : 0,
            'creator' => Auth::id(),
        ]);

        // Send email to staff if notification is enabled
        if ($reminder->notify_by_email) {
            $assignedStaff = Staff::find($request->staff);
            if ($assignedStaff && $assignedStaff->email) {
                $subject = "Reminder: Invoice #" . $invoice->number;
                $messageBody = "
                    <p>Hi {$assignedStaff->firstname},</p>
                    <p>This is a reminder regarding Invoice <strong>#{$invoice->number}</strong>.</p>
                    <p><strong>Date:</strong> " . \Carbon\Carbon::parse($request->date)->format('d M, Y H:i') . "</p>
                    <p><strong>Description:</strong> {$request->description}</p>
                    <p><a href='" . route('admin.invoices.show', $invoice->id) . "' class='btn'>View Invoice</a></p>
                ";
                
                $this->emailService->sendCustom($assignedStaff->email, $subject, $messageBody);
            }
        }

        $this->logActivity($id, 'Reminder set for Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

        return redirect()->back()->with('success', 'Reminder added successfully!');
    }

    private function logActivity($invoiceId, $description)
    {
        $staff = Auth::user();
        SalesActivity::create([
            'rel_type' => 'invoice',
            'rel_id' => $invoiceId,
            'description' => $description,
            'staffid' => $staff->staffid ?? 0,
            'full_name' => ($staff->firstname ?? '') . ' ' . ($staff->lastname ?? ''),
            'date' => now(),
        ]);
    }
}