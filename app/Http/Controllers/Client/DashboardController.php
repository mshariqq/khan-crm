<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ClientService;
use Illuminate\Http\Request;

class DashboardController extends ClientBaseController
{
    protected $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function index()
    {
        $contact = $this->getContact();
        $client = $this->getClient();
        $data = $this->clientService->getClientDashboardData($client);

        return view('client.dashboard', [
            'data' => $data,
            'user' => $contact,
            'client' => $client
        ]);
    }
}
