<?php

namespace App\Http\Controllers\Client;

use App\Models\Estimate;
use Illuminate\Http\Request;

class EstimateController extends ClientBaseController
{
    public function index(Request $request)
    {
        $client = $this->getClient();
        $query = $client->estimates();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('number', 'like', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $estimates = $query->orderBy('date', 'desc')->paginate(10)->withQueryString();
        
        return view('client.estimates.index', compact('estimates'));
    }

    public function show($id)
    {
        $client = $this->getClient();
        $estimate = $client->estimates()->with(['items', 'currency'])->findOrFail($id);
        
        return view('client.estimates.show', compact('estimate'));
    }

    public function export(Request $request)
    {
        $client = $this->getClient();
        $query = $client->estimates();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('number', 'like', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $estimates = $query->get();

        $filename = "estimates_export_" . now()->format('YmdHis') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Estimate #', 'Date', 'Expiry Date', 'Total', 'Status', 'Created Date']);

        foreach ($estimates as $estimate) {
            $statusLabel = 'Unknown';
            if($estimate->status == Estimate::STATUS_DRAFT) $statusLabel = 'Draft';
            elseif($estimate->status == Estimate::STATUS_SENT) $statusLabel = 'Sent';
            elseif($estimate->status == Estimate::STATUS_ACCEPTED) $statusLabel = 'Accepted';
            elseif($estimate->status == Estimate::STATUS_DECLINED) $statusLabel = 'Declined';

            fputcsv($handle, [
                $estimate->number,
                $estimate->date,
                $estimate->expirydate,
                app_format_money($estimate->total, $estimate->currency),
                $statusLabel,
                $estimate->created_at,
            ]);
        }

        fclose($handle);
        exit;
    }
}
