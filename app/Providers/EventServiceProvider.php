<?php

namespace App\Providers;

use App\Events\DiscussionCommented;
use App\Events\DiscussionCreated;
use App\Events\EstimateConvertedToInvoice;
use App\Events\InvoiceCreated;
use App\Events\InvoicePaid;
use App\Events\LeadConverted;
use App\Events\MilestoneCompleted;
use App\Events\MilestoneCreated;
use App\Events\PaymentRecorded;
use App\Events\ProjectCompleted;
use App\Events\ProjectCreated;
use App\Events\ProjectDeleted;
use App\Events\ProjectFileDeleted;
use App\Events\ProjectFileUploaded;
use App\Events\ProjectMemberAdded;
use App\Events\ProjectMemberRemoved;
use App\Events\ProjectStatusChanged;
use App\Events\ProjectUpdated;
use App\Events\TaskAssigned;
use App\Events\TaskChecklistCompleted;
use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskReopened;
use App\Events\TaskTimerStarted;
use App\Events\TaskTimerStopped;
use App\Events\TaskUnassigned;
use App\Events\TaskUpdated;
use App\Listeners\LogEstimateConverted;
use App\Listeners\LogInvoiceCreated;
use App\Listeners\LogInvoicePaid;
use App\Listeners\LogLeadConverted;
use App\Listeners\LogPaymentRecorded;
use App\Listeners\LogProjectActivity;
use App\Listeners\LogTaskActivity;
use App\Listeners\FinalizeProjectInvoices;
use App\Listeners\AutoInvoiceMilestone;
use App\Models\Project as ProjectModel;
use App\Models\Task as TaskModel;
use App\Observers\ProjectObserver;
use App\Observers\TaskObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InvoicePaid::class => [LogInvoicePaid::class],
        PaymentRecorded::class => [LogPaymentRecorded::class],
        InvoiceCreated::class => [LogInvoiceCreated::class],
        LeadConverted::class => [LogLeadConverted::class],
        EstimateConvertedToInvoice::class => [LogEstimateConverted::class],
        TaskCreated::class => [LogTaskActivity::class],
        TaskUpdated::class => [LogTaskActivity::class],
        TaskDeleted::class => [LogTaskActivity::class],
        TaskCompleted::class => [LogTaskActivity::class],
        TaskReopened::class => [LogTaskActivity::class],
        TaskAssigned::class => [LogTaskActivity::class],
        TaskUnassigned::class => [LogTaskActivity::class],
        TaskChecklistCompleted::class => [LogTaskActivity::class],
        TaskTimerStarted::class => [LogTaskActivity::class],
        TaskTimerStopped::class => [LogTaskActivity::class],

        ProjectCreated::class => [LogProjectActivity::class . '@handleProjectCreated'],
        ProjectUpdated::class => [LogProjectActivity::class . '@handleProjectUpdated'],
        ProjectDeleted::class => [LogProjectActivity::class . '@handleProjectDeleted'],
        ProjectStatusChanged::class => [LogProjectActivity::class . '@handleProjectStatusChanged'],
        ProjectCompleted::class => [
            LogProjectActivity::class . '@handleProjectCompleted',
            FinalizeProjectInvoices::class,
        ],
        ProjectMemberAdded::class => [LogProjectActivity::class . '@handleProjectMemberAdded'],
        ProjectMemberRemoved::class => [LogProjectActivity::class . '@handleProjectMemberRemoved'],
        ProjectFileUploaded::class => [LogProjectActivity::class . '@handleProjectFileUploaded'],
        ProjectFileDeleted::class => [LogProjectActivity::class . '@handleProjectFileDeleted'],
        DiscussionCreated::class => [LogProjectActivity::class . '@handleDiscussionCreated'],
        DiscussionCommented::class => [LogProjectActivity::class . '@handleDiscussionCommented'],
        MilestoneCreated::class => [LogProjectActivity::class . '@handleMilestoneCreated'],
        MilestoneCompleted::class => [
            LogProjectActivity::class . '@handleMilestoneCompleted',
            AutoInvoiceMilestone::class,
        ],
    ];

    protected $observers = [
        TaskModel::class => [TaskObserver::class],
        ProjectModel::class => [ProjectObserver::class],
    ];

    public function boot(): void
    {
        parent::boot();
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
