<?php

namespace App\Events;

use App\Models\InvoicePaymentRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentRecorded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public InvoicePaymentRecord $payment,
        public ?int $staffId = null
    ) {}
}
