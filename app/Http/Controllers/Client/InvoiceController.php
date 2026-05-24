<?php

namespace App\Http\Controllers\Client;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends ClientBaseController
{
    public function index(Request $request)
    {
        $client = $this->getClient();
        $query = $client->invoices();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                  ->orWhere('prefix', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('date', 'desc')->paginate(10)->withQueryString();
        
        return view('client.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $client = $this->getClient();
        $invoice = $client->invoices()->with(['items', 'payments', 'currency'])->findOrFail($id);
        
        return view('client.invoices.show', compact('invoice'));
    }

    public function export(Request $request)
    {
        $client = $this->getClient();
        $query = $client->invoices();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                  ->orWhere('prefix', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->get();

        $filename = "invoices_export_" . now()->format('YmdHis') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Invoice #', 'Date', 'Due Date', 'Total', 'Status', 'Created Date']);

        foreach ($invoices as $invoice) {
            $statusLabel = 'Unknown';
            if($invoice->status == Invoice::STATUS_UNPAID) $statusLabel = 'Unpaid';
            elseif($invoice->status == Invoice::STATUS_PARTIALLY_PAID) $statusLabel = 'Partially Paid';
            elseif($invoice->status == Invoice::STATUS_PAID) $statusLabel = 'Paid';
            elseif($invoice->status == Invoice::STATUS_CANCELLED) $statusLabel = 'Cancelled';

            fputcsv($handle, [
                ($invoice->prefix ?? '') . $invoice->number,
                $invoice->date,
                $invoice->duedate,
                app_format_money($invoice->total, $invoice->currency),
                $statusLabel,
                $invoice->created_at,
            ]);
        }

        fclose($handle);
        exit;
    }
}
