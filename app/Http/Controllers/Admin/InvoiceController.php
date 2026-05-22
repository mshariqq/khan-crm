<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
use App\Models\Itemable;
use App\Models\Reminder;
use App\Models\SalesActivity;
use App\Models\InvoicePaymentRecord;
use App\Services\EmailService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        if (!Auth::user()->can('invoices_view')) {
            abort(403, 'Unauthorized action.');
        }

        $query = Invoice::query()->with(['client', 'currencyRel', 'creator']);

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
        $paid = Invoice::where('status', 3)->count();
        $unpaid = Invoice::where('status', 1)->count();
        $overdue = Invoice::where('status', 1)->where('duedate', '<', now())->count();

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

    public function store(Request $request)
    {
        if (!Auth::user()->can('invoices_create')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'clientid' => 'required|integer',
            'currency' => 'required|integer',
            'date' => 'required|date',
            'duedate' => 'nullable|date',
            'subtotal' => 'nullable|numeric',
            'total_tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'discount_total' => 'nullable|numeric',
            'discount_type' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'prefix' => 'nullable|string|max:50',
            'number_format' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'subscription_id' => 'nullable|integer',
            'sale_agent' => 'nullable|integer',
            'billing_street' => 'nullable|string',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_zip' => 'nullable|string|max:50',
            'billing_country' => 'nullable|integer',
            'shipping_street' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_zip' => 'nullable|string|max:50',
            'shipping_country' => 'nullable|integer',
            'include_shipping' => 'nullable|boolean',
            'show_shipping_on_invoice' => 'nullable|boolean',
            'show_quantity_as' => 'nullable|string|max:50',
            'clientnote' => 'nullable|string',
            'adminnote' => 'nullable|string',
            'terms' => 'nullable|string',
            'recurring' => 'nullable|integer',
            'recurring_type' => 'nullable|string|max:50',
            'cycles' => 'nullable|integer',
            'cancel_overdue_reminders' => 'nullable|boolean',
            'allowed_payment_modes' => 'nullable|string',
        ]);

        // Set defaults for nullable numeric fields to avoid NULL errors
        $validated['subtotal'] = $validated['subtotal'] ?? 0;
        $validated['total_tax'] = $validated['total_tax'] ?? 0;
        $validated['adjustment'] = $validated['adjustment'] ?? 0;
        $validated['discount_percent'] = $validated['discount_percent'] ?? 0;
        $validated['discount_total'] = $validated['discount_total'] ?? 0;
        $validated['include_shipping'] = $validated['include_shipping'] ?? 0;
        $validated['show_shipping_on_invoice'] = $validated['show_shipping_on_invoice'] ?? 0;
        $validated['recurring'] = $validated['recurring'] ?? 0;
        $validated['cancel_overdue_reminders'] = $validated['cancel_overdue_reminders'] ?? 0;
        
        $invoiceData = $validated;
        $invoiceData['number'] = (int)preg_replace('/[^0-9]/', '', $validated['number']);
        $invoiceData['datecreated'] = now();
        $invoiceData['addedfrom'] = Auth::id();
        $invoiceData['hash'] = Str::random(32);
        $invoiceData['sent'] = 0;
        
        // Required NOT NULL fields with defaults from migration
        $invoiceData['discount_type'] = $request->discount_type ?? 'percent';
        $invoiceData['show_quantity_as'] = $request->show_quantity_as ?? 1;
        $invoiceData['sale_agent'] = $request->sale_agent ?? 0;
        $invoiceData['project_id'] = $request->project_id ?? 0;
        $invoiceData['subscription_id'] = $request->subscription_id ?? 0;
        $invoiceData['billing_country'] = $request->billing_country ?? 0;
        $invoiceData['shipping_country'] = $request->shipping_country ?? 0;
        $invoiceData['prefix'] = $request->prefix ?? '';
        $invoiceData['number_format'] = $request->number_format ?? 0;
        
        // Only add nullable fields if they have values
        if (!empty($request->duedate)) $invoiceData['duedate'] = $request->duedate;
        
        $invoice = Invoice::create($invoiceData);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> created');

        // Save items
        if ($request->has('item_description')) {
            $descriptions = $request->item_description;
            $qtys = $request->item_qty ?? [];
            $rates = $request->item_rate ?? [];
            $amounts = $request->item_amount ?? [];
            $taxIds = $request->item_tax_id ?? [];

            foreach ($descriptions as $index => $description) {
                if (!empty($description)) {
                    $taxRate = 0;
                    if (!empty($taxIds[$index])) {
                        $tax = Tax::find($taxIds[$index]);
                        if ($tax) {
                            $taxRate = $tax->taxrate;
                        }
                    }

                    Itemable::create([
                        'rel_id' => $invoice->id,
                        'rel_type' => 'invoice',
                        'description' => $description,
                        'qty' => $qtys[$index] ?? 1,
                        'rate' => $rates[$index] ?? 0,
                        'amount' => $amounts[$index] ?? 0,
                        'tax_rate' => $taxRate,
                        'item_order' => $index,
                    ]);
                }
            }
        }

        if ($request->filled('referer')) {
            return redirect($request->referer)->with('success', 'Invoice created successfully!');
        }

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice created successfully!');
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

        $invoice = Invoice::findOrFail($id);
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

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'clientid' => 'required|integer',
            'currency' => 'required|integer',
            'date' => 'required|date',
            'duedate' => 'nullable|date',
            'subtotal' => 'nullable|numeric',
            'total_tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric',
            'discount_total' => 'nullable|numeric',
            'discount_type' => 'nullable|string|max:50',
            'status' => 'required|integer',
            'prefix' => 'nullable|string|max:50',
            'number_format' => 'nullable|integer',
            'project_id' => 'nullable|integer',
            'subscription_id' => 'nullable|integer',
            'sale_agent' => 'nullable|integer',
            'billing_street' => 'nullable|string',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_zip' => 'nullable|string|max:50',
            'billing_country' => 'nullable|integer',
            'shipping_street' => 'nullable|string',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_state' => 'nullable|string|max:255',
            'shipping_zip' => 'nullable|string|max:50',
            'shipping_country' => 'nullable|integer',
            'include_shipping' => 'nullable|boolean',
            'show_shipping_on_invoice' => 'nullable|boolean',
            'show_quantity_as' => 'nullable|string|max:50',
            'clientnote' => 'nullable|string',
            'adminnote' => 'nullable|string',
            'terms' => 'nullable|string',
            'recurring' => 'nullable|integer',
            'recurring_type' => 'nullable|string|max:50',
            'cycles' => 'nullable|integer',
            'cancel_overdue_reminders' => 'nullable|boolean',
            'allowed_payment_modes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($id);
        $validated['number'] = (int)preg_replace('/[^0-9]/', '', $validated['number']);
        $invoice->update($validated);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> updated');

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
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 3]);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 2]);
        }

        // Send payment recorded email
        if ($invoice->client && $invoice->client->email) {
            $data = [
                'contact_firstname' => $invoice->client->company,
                'contact_lastname' => '',
                'payment_total' => ($invoice->currency->symbol ?? '$') . number_format($request->amount, 2),
                'payment_date' => \Carbon\Carbon::parse($request->date)->format('d M, Y'),
                'invoice_number' => ($invoice->prefix ?? '') . $invoice->number,
                'invoice_link' => config('app.url') . '/invoices/' . $invoice->id . '/' . $invoice->hash,
            ];

            $this->emailService->send($invoice->client->email, 'invoice-payment-recorded', $data);
        }

        $this->logActivity($invoice->id, 'Payment of <b>' . ($invoice->currency->symbol ?? '$') . number_format($request->amount, 2) . '</b> recorded for Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Payment recorded!');
    }

    public function markAsPaid($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 3]);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> marked as paid');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice marked as paid!');
    }

    public function markAsCancelled($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 4]);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> cancelled');

        return redirect()->route('admin.invoices.show', $invoice->id)->with('success', 'Invoice cancelled!');
    }

    public function sendInvoice($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $invoice = Invoice::with(['client', 'currency'])->findOrFail($id);
        
        if ($invoice->client && $invoice->client->email) {
            $data = [
                'contact_firstname' => $invoice->client->company, // Using company as fallback
                'contact_lastname' => '',
                'invoice_number' => ($invoice->prefix ?? '') . $invoice->number,
                'invoice_status' => $invoice->status == 3 ? 'Paid' : 'Unpaid',
                'invoice_link' => config('app.url') . '/invoices/' . $invoice->id . '/' . $invoice->hash,
            ];

            $this->emailService->send($invoice->client->email, 'invoice-send-to-client', $data);
        }

        $invoice->update(['sent' => 1, 'datesend' => now()]);

        $this->logActivity($invoice->id, 'Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b> sent to client');

        return redirect()->back()->with('success', 'Invoice sent to client!');
    }

    public function print($id)
    {
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

    public function deleteReminder($id)
    {
        if (!Auth::user()->can('invoices_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $reminder = Reminder::findOrFail($id);
        $invoiceId = $reminder->rel_id;
        $reminder->delete();

        $invoice = Invoice::find($invoiceId);
        if ($invoice) {
            $this->logActivity($invoiceId, 'Reminder removed from Invoice <b>#' . ($invoice->prefix ?? '') . $invoice->number . '</b>');
        }

        return redirect()->back()->with('success', 'Reminder deleted!');
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