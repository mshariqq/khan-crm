<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $results = [];
        $limit = 5;

        try {
            // Clients
            $clients = \App\Models\Client::where('company', 'like', "%{$search}%")
                ->where('active', 1)
                ->limit($limit)
                ->get();
            
            foreach ($clients as $c) {
                $results[] = [
                    'type' => 'Client',
                    'icon' => 'bi-person',
                    'icon_bg' => 'bg-blue-50 text-blue-600',
                    'title' => $c->company,
                    'subtitle' => $c->email ?? 'No email',
                    'url' => route('admin.clients.show', $c->userid),
                ];
            }

            // Leads
            $leads = \App\Models\Lead::where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            })
            ->limit($limit)
            ->get();
            
            foreach ($leads as $l) {
                $results[] = [
                    'type' => 'Lead',
                    'icon' => 'bi-megaphone',
                    'icon_bg' => 'bg-amber-50 text-amber-600',
                    'title' => $l->name,
                    'subtitle' => $l->company ?: 'No company',
                    'url' => route('admin.leads.show', $l->id),
                ];
            }

            // Projects
            $projects = \App\Models\Project::where('name', 'like', "%{$search}%")
                ->limit($limit)
                ->get();
            
            foreach ($projects as $p) {
                $results[] = [
                    'type' => 'Project',
                    'icon' => 'bi-kanban',
                    'icon_bg' => 'bg-emerald-50 text-emerald-600',
                    'title' => $p->name,
                    'subtitle' => $p->status == 1 ? 'Active' : 'Inactive',
                    'url' => route('admin.projects.show', $p->id),
                ];
            }

            // Tasks (uses 'name' not 'title')
            $tasks = \App\Models\Task::where('name', 'like', "%{$search}%")
                ->limit($limit)
                ->get();
            
            foreach ($tasks as $t) {
                $results[] = [
                    'type' => 'Task',
                    'icon' => 'bi-check2-square',
                    'icon_bg' => 'bg-purple-50 text-purple-600',
                    'title' => $t->name,
                    'subtitle' => $t->status == 2 ? 'Done' : 'Pending',
                    'url' => route('admin.tasks.show', $t->id),
                ];
            }

            // Tickets
            $tickets = \App\Models\Ticket::where('subject', 'like', "%{$search}%")
                ->limit($limit)
                ->get();
            
            foreach ($tickets as $t) {
                $results[] = [
                    'type' => 'Ticket',
                    'icon' => 'bi-ticket-perforated',
                    'icon_bg' => 'bg-red-50 text-red-600',
                    'title' => $t->subject,
                    'subtitle' => '#' . $t->ticketid,
                    'url' => route('admin.tickets.show', $t->ticketid),
                ];
            }

            // Invoices
            $invoices = \App\Models\Invoice::where('number', 'like', "%{$search}%")
                ->limit($limit)
                ->get();
            
            foreach ($invoices as $i) {
                $results[] = [
                    'type' => 'Invoice',
                    'icon' => 'bi-receipt',
                    'icon_bg' => 'bg-cyan-50 text-cyan-600',
                    'title' => $i->number,
                    'subtitle' => number_format($i->total, 2),
                    'url' => route('admin.invoices.show', $i->id),
                ];
            }

        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($results);
    }
}