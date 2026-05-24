<?php

namespace App\Services;

use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function dispatch(string $event, array $payload): void
    {
        $webhooks = Webhook::where('is_active', true)
            ->where(function ($q) use ($event) {
                $q->where('events', 'like', "%\"{$event}\"%")
                  ->orWhere('events', 'like', '%"*"%');
            })->get();

        foreach ($webhooks as $webhook) {
            try {
                $signature = hash_hmac('sha256', json_encode($payload), $webhook->secret ?? '');

                $response = Http::timeout(10)
                    ->withHeaders([
                        'X-Webhook-Signature' => $signature,
                        'X-Webhook-Event' => $event,
                        'Content-Type' => 'application/json',
                    ])
                    ->post($webhook->url, $payload);

                $webhook->update([
                    'last_triggered_at' => now(),
                    'failure_count' => $response->successful() ? 0 : ($webhook->failure_count + 1),
                ]);
            } catch (\Exception $e) {
                Log::warning("Webhook dispatch failed for {$webhook->name}: " . $e->getMessage());
                $webhook->increment('failure_count');
            }
        }
    }

    public function dispatchProjectEvent(string $event, $project): void
    {
        $this->dispatch($event, [
            'event' => $event,
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'status' => $project->status,
                'client_id' => $project->clientid,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
