<?php

namespace App\Services;

use App\Models\Client;

class ClientService extends BaseService
{
    public function getClientDashboardData(Client $client)
    {
        return [
            'active_projects' => $client->projects()->where('status', 1)->get(),
            'unpaid_invoices' => $client->invoices()->where('status', 1)->get(),
        ];
    }

    public function updatePortalSettings(Client $client, array $data)
    {
        return $client->update($data);
    }
}
