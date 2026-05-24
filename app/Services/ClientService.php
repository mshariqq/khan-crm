<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;

class ClientService extends BaseService
{
    public function getClientDashboardData(Client $client)
    {
        return [
            'active_projects' => $client->projects()->where('status', Project::STATUS_ACTIVE)->get(),
            'unpaid_invoices' => $client->invoices()->where('status', Invoice::STATUS_UNPAID)->get(),
        ];
    }

    public function updatePortalSettings(Client $client, array $data)
    {
        return $client->update($data);
    }
}
