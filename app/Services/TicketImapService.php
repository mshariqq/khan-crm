<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketEmailIntegration;
use App\Models\Contact;
use App\Models\Client;
use App\Models\Department;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketPipeLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TicketImapService
{
    public function fetchEmails()
    {
        $integrations = TicketEmailIntegration::where('active', 1)->get();

        foreach ($integrations as $integration) {
            $this->processIntegration($integration);
        }
    }

    protected function processIntegration(TicketEmailIntegration $integration)
    {
        $encryption = $integration->encryption ? '/' . $integration->encryption : '';
        $mailbox = "{" . $integration->imap_server . ":" . $integration->port . "/imap" . $encryption . "/ssl/novalidate-cert}" . $integration->folder;

        try {
            $inbox = imap_open($mailbox, $integration->email, $integration->password);
            if (!$inbox) {
                Log::error("Failed to connect to IMAP for {$integration->email}: " . imap_last_error());
                return;
            }

            $criteria = $integration->only_loop_on_unseen_emails ? 'UNSEEN' : 'ALL';
            $emails = imap_search($inbox, $criteria);

            if ($emails) {
                rsort($emails);
                foreach ($emails as $emailNumber) {
                    $this->processEmail($inbox, $emailNumber, $integration);
                }
            }

            $integration->update(['last_run' => now()]);
            imap_close($inbox);
        } catch (\Exception $e) {
            Log::error("Error processing IMAP for {$integration->email}: " . $e->getMessage());
        }
    }

    protected function processEmail($inbox, $emailNumber, TicketEmailIntegration $integration)
    {
        $overview = imap_fetch_overview($inbox, $emailNumber, 0)[0];
        $header = imap_headerinfo($inbox, $emailNumber);
        $structure = imap_fetchstructure($inbox, $emailNumber);
        
        $from = $header->from[0]->mailbox . "@" . $header->from[0]->host;
        $subject = $overview->subject ?? '(No Subject)';
        $messageId = $header->message_id ?? null;
        $inReplyTo = $header->in_reply_to ?? null;
        $references = $header->references ?? null;
        
        $body = $this->getEmailBody($inbox, $emailNumber);

        // Check if it's a reply to an existing ticket
        $ticket = $this->findExistingTicket($subject, $inReplyTo, $references);

        if ($ticket) {
            $this->addReplyToTicket($ticket, $from, $body, $messageId, $inReplyTo, $references);
            TicketPipeLog::create([
                'date' => now(),
                'email_to' => $integration->email,
                'name' => $from,
                'subject' => $subject,
                'message' => 'Reply added to ticket #' . $ticket->ticketid,
                'email' => $from,
                'status' => 'success'
            ]);
        } else {
            $this->createNewTicket($integration, $from, $subject, $body, $messageId, $references);
            TicketPipeLog::create([
                'date' => now(),
                'email_to' => $integration->email,
                'name' => $from,
                'subject' => $subject,
                'message' => 'New ticket created',
                'email' => $from,
                'status' => 'success'
            ]);
        }

        if ($integration->delete_after_import) {
            imap_delete($inbox, $emailNumber);
        } else if ($integration->only_loop_on_unseen_emails) {
            imap_setflag_full($inbox, $emailNumber, "\\Seen");
        }
    }

    protected function findExistingTicket($subject, $inReplyTo, $references)
    {
        // 1. Try by In-Reply-To or References
        if ($inReplyTo) {
            $reply = TicketReply::where('message_id', $inReplyTo)->first();
            if ($reply) return $reply->ticket;

            $ticket = Ticket::where('message_id', $inReplyTo)->first();
            if ($ticket) return $ticket;
        }

        // 2. Try by Ticket ID in Subject [#123]
        if (preg_match('/\[#(\d+)\]/', $subject, $matches)) {
            return Ticket::find($matches[1]);
        }

        return null;
    }

    protected function createNewTicket($integration, $from, $subject, $body, $messageId, $references)
    {
        $contact = Contact::where('email', $from)->first();
        $userId = $contact ? $contact->userid : 0;
        $contactId = $contact ? $contact->id : 0;
        $name = $contact ? ($contact->firstname . ' ' . $contact->lastname) : $from;

        $status = TicketStatus::where('isdefault', 1)->first();

        return Ticket::create([
            'subject' => $subject,
            'message' => $body,
            'department' => $integration->department_id,
            'priority' => $integration->priority_id,
            'status' => $status ? $status->ticketstatusid : 1,
            'userid' => $userId,
            'contactid' => $contactId,
            'email' => $from,
            'name' => $name,
            'date' => now(),
            'admin' => 0,
            'ticketkey' => Str::random(32),
            'message_id' => $messageId,
            'references' => $references,
        ]);
    }

    protected function addReplyToTicket($ticket, $from, $body, $messageId, $inReplyTo, $references)
    {
        $contact = Contact::where('email', $from)->first();
        $userId = $contact ? $contact->userid : 0;
        $contactId = $contact ? $contact->id : 0;
        $name = $contact ? ($contact->firstname . ' ' . $contact->lastname) : $from;

        $reply = TicketReply::create([
            'ticketid' => $ticket->ticketid,
            'userid' => $userId,
            'contactid' => $contactId,
            'name' => $name,
            'email' => $from,
            'message' => $body,
            'date' => now(),
            'admin' => 0,
            'message_id' => $messageId,
            'in_reply_to' => $inReplyTo,
            'references' => $references,
        ]);

        $ticket->update([
            'lastreply' => now(),
            'adminreplying' => 0,
            'clientread' => 0,
            'adminread' => 0
        ]);

        return $reply;
    }

    protected function getEmailBody($inbox, $emailNumber)
    {
        $structure = imap_fetchstructure($inbox, $emailNumber);
        if (!isset($structure->parts)) {
            return $this->getPart($inbox, $emailNumber, "1");
        } else {
            return $this->getPart($inbox, $emailNumber, "1.1") ?: $this->getPart($inbox, $emailNumber, "1");
        }
    }

    protected function getPart($inbox, $emailNumber, $partNumber)
    {
        $data = imap_fetchbody($inbox, $emailNumber, $partNumber);
        $structure = imap_fetchstructure($inbox, $emailNumber);
        
        // Find encoding
        $encoding = 0;
        if ($partNumber == "1") {
            $encoding = $structure->encoding;
        } else {
            $parts = explode(".", $partNumber);
            $currentPart = $structure;
            foreach ($parts as $p) {
                $currentPart = $currentPart->parts[$p - 1];
            }
            $encoding = $currentPart->encoding;
        }

        if ($encoding == 3) return base64_decode($data);
        if ($encoding == 4) return quoted_printable_decode($data);
        return $data;
    }
}
