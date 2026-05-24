<?php

namespace App\Providers;

use App\Events\EstimateConvertedToInvoice;
use App\Events\InvoiceCreated;
use App\Events\InvoicePaid;
use App\Events\LeadConverted;
use App\Events\PaymentRecorded;
use App\Listeners\LogEstimateConverted;
use App\Listeners\LogInvoiceCreated;
use App\Listeners\LogInvoicePaid;
use App\Listeners\LogLeadConverted;
use App\Listeners\LogPaymentRecorded;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoicePaid::class => [LogInvoicePaid::class],
        PaymentRecorded::class => [LogPaymentRecorded::class],
        InvoiceCreated::class => [LogInvoiceCreated::class],
        LeadConverted::class => [LogLeadConverted::class],
        EstimateConvertedToInvoice::class => [LogEstimateConverted::class],
    ];

    public function boot(): void {}
}
