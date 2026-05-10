<?php

namespace App\Services;

use App\Models\LeadActivityLog;
use Illuminate\Support\Facades\Auth;

class LeadActivityLogService
{
    public static function log($leadId, $description, $additionalData = null)
    {
        $staff = Auth::user();
        
        LeadActivityLog::create([
            'leadid' => $leadId,
            'description' => $description,
            'additional_data' => $additionalData ? json_encode($additionalData) : null,
            'date' => now(),
            'staffid' => $staff ? $staff->staffid : null,
            'full_name' => $staff ? $staff->firstname . ' ' . $staff->lastname : 'System',
        ]);
    }

    public static function leadCreated($lead)
    {
        self::log($lead->id, 'Lead created', [
            'name' => $lead->name,
            'company' => $lead->company,
            'email' => $lead->email,
        ]);
    }

    public static function leadUpdated($lead, $changes = [])
    {
        self::log($lead->id, 'Lead updated', $changes);
    }

    public static function noteAdded($lead, $noteId)
    {
        self::log($lead->id, 'Note added to lead');
    }

    public static function reminderSet($lead, $reminderId)
    {
        self::log($lead->id, 'Reminder set for lead');
    }

    public static function convertedToClient($lead, $clientId, $clientName)
    {
        self::log($lead->id, 'Lead converted to client: ' . $clientName, [
            'client_id' => $clientId,
            'client_name' => $clientName,
        ]);
    }

    public static function markedAsLost($lead)
    {
        self::log($lead->id, 'Lead marked as lost');
    }

    public static function markedAsJunk($lead)
    {
        self::log($lead->id, 'Lead marked as junk');
    }

    public static function statusChanged($lead, $oldStatus, $newStatus)
    {
        self::log($lead->id, 'Status changed from ' . $oldStatus . ' to ' . $newStatus, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    public static function assignedTo($lead, $staffName)
    {
        self::log($lead->id, 'Lead assigned to ' . $staffName, [
            'staff_name' => $staffName,
        ]);
    }

    public static function gdprRequestSubmitted($lead, $type)
    {
        self::log($lead->id, 'GDPR ' . $type . ' request submitted');
    }

    public static function taskLinked($lead, $taskId, $taskName)
    {
        self::log($lead->id, 'Task linked: ' . $taskName, [
            'task_id' => $taskId,
        ]);
    }

    public static function proposalLinked($lead, $proposalId, $proposalSubject)
    {
        self::log($lead->id, 'Proposal linked: ' . $proposalSubject, [
            'proposal_id' => $proposalId,
        ]);
    }
}