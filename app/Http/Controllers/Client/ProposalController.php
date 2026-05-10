<?php

namespace App\Http\Controllers\Client;

use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends ClientBaseController
{
    public function index(Request $request)
    {
        $client = $this->getClient();
        $query = $client->proposals();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('subject', 'like', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->orderBy('date', 'desc')->paginate(10)->withQueryString();
        
        return view('client.proposals.index', compact('proposals'));
    }

    public function show($id)
    {
        $client = $this->getClient();
        $proposal = $client->proposals()->with(['items', 'currencyRel'])->findOrFail($id);
        
        return view('client.proposals.show', compact('proposal'));
    }

    public function export(Request $request)
    {
        $client = $this->getClient();
        $query = $client->proposals();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('subject', 'like', '%' . $search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->get();

        $filename = "proposals_export_" . now()->format('YmdHis') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['Subject', 'Date', 'Open Till', 'Total', 'Status', 'Created Date']);

        foreach ($proposals as $proposal) {
            $statusLabel = 'Unknown';
            if($proposal->status == 1) $statusLabel = 'Open';
            elseif($proposal->status == 2) $statusLabel = 'Accepted';
            elseif($proposal->status == 3) $statusLabel = 'Declined';

            fputcsv($handle, [
                $proposal->subject,
                $proposal->date,
                $proposal->open_till,
                ($proposal->currencyRel->symbol ?? '$') . number_format($proposal->total, 2),
                $statusLabel,
                $proposal->created_at,
            ]);
        }

        fclose($handle);
        exit;
    }
}
