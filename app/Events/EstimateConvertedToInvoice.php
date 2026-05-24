<?php

namespace App\Events;

use App\Models\Estimate;
use App\Models\Invoice;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstimateConvertedToInvoice
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Estimate $estimate,
        public Invoice $invoice,
        public ?int $staffId = null
    ) {}
}
