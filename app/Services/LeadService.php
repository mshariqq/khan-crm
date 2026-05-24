<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\Client;
use App\Models\Contact;
use App\Services\LeadActivityLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeadService extends BaseService
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function checkExistingClient(string $company, string $email): ?array
    {
        $existingClient = Client::where('company', $company)->first();
        if ($existingClient) {
            return [
                'type' => 'company',
                'client' => $existingClient,
                'message' => 'A client with the company "' . $company . '" already exists.',
            ];
        }

        $existingContact = Contact::where('email', $email)->first();
        if ($existingContact) {
            $client = Client::find($existingContact->userid);
            if ($client) {
                return [
                    'type' => 'contact',
                    'client' => $client,
                    'message' => 'A contact with email "' . $email . '" already belongs to client "' . $client->company . '".',
                ];
            }
        }

        return null;
    }

    public function convertToClient(Lead $lead, string $company, string $email): Client
    {
        $client = Client::create([
            'company' => $company,
            'phonenumber' => $lead->phonenumber ?? '',
            'address' => $lead->address ?? '',
            'city' => $lead->city ?? '',
            'state' => $lead->state ?? '',
            'zip' => $lead->zip ?? '',
            'country' => $lead->country ?? 0,
            'website' => $lead->website ?? '',
            'default_language' => $lead->default_language ?? '',
            'leadid' => $lead->id,
            'datecreated' => now(),
            'active' => 1,
        ]);

        $nameParts = explode(' ', trim($lead->name), 2);
        $firstname = $nameParts[0] ?? $lead->name;
        $lastname = $nameParts[1] ?? '';

        Contact::create([
            'userid' => $client->userid,
            'is_primary' => 1,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'phonenumber' => $lead->phonenumber ?? '',
            'title' => $lead->title ?? '',
            'datecreated' => now(),
            'active' => 1,
        ]);

        $lead->update([
            'client_id' => $client->userid,
            'date_converted' => now(),
            'status' => 5,
        ]);

        return $client;
    }

    public function sendWelcomeEmail(Client $client, string $email, string $firstname, string $lastname): void
    {
        $this->emailService->send($email, 'new-client-created', [
            'contact_firstname' => $firstname,
            'contact_lastname' => $lastname,
            'crm_url' => config('app.url') . '/login/client',
        ]);
    }
}
