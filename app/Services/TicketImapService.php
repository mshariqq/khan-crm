<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketAttachment;
use App\Models\TicketEmailIntegration;
use App\Models\Contact;
use App\Models\Client;
use App\Models\Department;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketPipeLog;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
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
        $encryption = strtolower($integration->encryption ?? '');
        $flags = '/imap';
        if ($encryption === 'ssl') {
            $flags .= '/ssl/novalidate-cert';
        } elseif ($encryption === 'tls') {
            $flags .= '/tls/novalidate-cert';
        }
        $mailbox = "{" . $integration->imap_server . ":" . $integration->port . $flags . "}" . $integration->folder;

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

        $attachmentParts = $this->findAttachmentParts($structure, '');

        if ($ticket) {
            $reply = $this->addReplyToTicket($ticket, $from, $body, $messageId, $inReplyTo, $references);
            if ($attachmentParts) {
                $this->saveAttachments($inbox, $emailNumber, $attachmentParts, $ticket->ticketid, $reply->id);
            }
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
            $newTicket = $this->createNewTicket($integration, $from, $subject, $body, $messageId, $references);
            if ($attachmentParts) {
                $this->saveAttachments($inbox, $emailNumber, $attachmentParts, $newTicket->ticketid, null);
            }
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
        // 1. Try by In-Reply-To
        if ($inReplyTo) {
            $result = $this->matchMessageId(trim($inReplyTo, ' <>'));
            if ($result) return $result;
        }

        // 2. Try by References (iterate all message IDs in the chain)
        if ($references) {
            $refIds = preg_split('/\s+/', $references);
            foreach ($refIds as $refId) {
                $refId = trim($refId, ' <>');
                if (!$refId) continue;
                $result = $this->matchMessageId($refId);
                if ($result) return $result;
            }
        }

        // 3. Try by Ticket ID in Subject [#123]
        if (preg_match('/\[#(\d+)\]/', $subject, $matches)) {
            return Ticket::find($matches[1]);
        }

        return null;
    }

    protected function matchMessageId($messageId)
    {
        $reply = TicketReply::where('message_id', $messageId)->first();
        if ($reply) return $reply->ticket;

        $ticket = Ticket::where('message_id', $messageId)->first();
        if ($ticket) return $ticket;

        // Also try with angle brackets in case stored format differs
        $withBrackets = '<' . $messageId . '>';
        $reply = TicketReply::where('message_id', $withBrackets)->first();
        if ($reply) return $reply->ticket;

        $ticket = Ticket::where('message_id', $withBrackets)->first();
        if ($ticket) return $ticket;

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

        $newReferences = $references
            ? $references . ' ' . ($ticket->message_id ?? '')
            : ($ticket->message_id ?? '');

        $ticket->update([
            'lastreply' => now(),
            'adminreplying' => 0,
            'staff_id_replying' => null,
            'clientread' => 0,
            'adminread' => 0,
            'message_id' => $messageId,
            'references' => trim($newReferences),
        ]);

        return $reply;
    }

    protected function getEmailBody($inbox, $emailNumber)
    {
        $structure = imap_fetchstructure($inbox, $emailNumber);

        // Find the best text/plain or text/html part from the MIME tree
        $body = $this->findBodyPart($inbox, $emailNumber, $structure, '');

        if ($body === null || $body === '') {
            // Fallback: try fetching part "1" if no MIME parts
            if (!isset($structure->parts)) {
                $data = $this->decodePart(imap_fetchbody($inbox, $emailNumber, "1"), $structure->encoding);
                $charset = $this->getCharset($structure);
                return $this->convertCharset($data, $charset);
            }
        }

        return $body ?? '';
    }

    protected function findBodyPart($inbox, $emailNumber, $structure, $partPrefix)
    {
        if (!isset($structure->parts)) {
            // Single part — fetch the body
            $partNum = $partPrefix ?: '1';
            $data = imap_fetchbody($inbox, $emailNumber, $partNum);
            $decoded = $this->decodePart($data, $structure->encoding);
            $charset = $this->getCharset($structure);
            return $this->convertCharset($decoded, $charset);
        }

        $primaryType = $structure->type;
        $subType = strtolower($structure->subtype ?? '');

        // multipart/alternative — prefer text/plain over text/html
        if ($primaryType == 1 && $subType === 'alternative') {
            $plain = null;
            $html = null;
            foreach ($structure->parts as $i => $part) {
                $childPartNum = $partPrefix ? "{$partPrefix}." . ($i + 1) : (string)($i + 1);
                $result = $this->findBodyPart($inbox, $emailNumber, $part, $childPartNum);
                if ($part->type == 0 && strtolower($part->subtype ?? '') === 'plain') {
                    $plain = $result;
                } elseif ($part->type == 0 && strtolower($part->subtype ?? '') === 'html') {
                    $html = $result;
                }
            }
            return $plain ?: $html ?: '';
        }

        // multipart/mixed, multipart/related, etc. — walk children
        if ($primaryType == 1) {
            foreach ($structure->parts as $i => $part) {
                $childPartNum = $partPrefix ? "{$partPrefix}." . ($i + 1) : (string)($i + 1);
                // Skip attachments
                if (isset($part->disposition) && strtolower($part->disposition) === 'attachment') {
                    continue;
                }
                $result = $this->findBodyPart($inbox, $emailNumber, $part, $childPartNum);
                if ($result) {
                    return $result;
                }
            }
            return '';
        }

        // text/plain or text/html leaf
        if ($primaryType == 0 || $primaryType == 2) {
            $partNum = $partPrefix ?: '1';
            $data = imap_fetchbody($inbox, $emailNumber, $partNum);
            $decoded = $this->decodePart($data, $structure->encoding);
            $charset = $this->getCharset($structure);
            return $this->convertCharset($decoded, $charset);
        }

        return '';
    }

    protected function decodePart($data, $encoding)
    {
        if ($encoding == 3) return base64_decode($data);
        if ($encoding == 4) return quoted_printable_decode($data);
        return $data;
    }

    protected function getCharset($structure)
    {
        if (isset($structure->parameters)) {
            foreach ($structure->parameters as $param) {
                if (strtolower($param->attribute) === 'charset') {
                    return strtolower($param->value);
                }
            }
        }
        if (isset($structure->dparameters)) {
            foreach ($structure->dparameters as $param) {
                if (strtolower($param->attribute) === 'charset') {
                    return strtolower($param->value);
                }
            }
        }
        return 'utf-8';
    }

    protected function convertCharset($data, $charset)
    {
        if (!$data || !$charset || strtolower($charset) === 'utf-8' || strtolower($charset) === 'us-ascii') {
            return $data;
        }
        $converted = iconv($charset, 'UTF-8//IGNORE', $data);
        return $converted !== false ? $converted : $data;
    }

    protected function findAttachmentParts($structure, $partPrefix)
    {
        $attachments = [];

        if (!isset($structure->parts)) {
            return $attachments;
        }

        foreach ($structure->parts as $i => $part) {
            $childPartNum = $partPrefix ? "{$partPrefix}." . ($i + 1) : (string)($i + 1);
            $filename = $this->getPartFilename($part);
            $disposition = isset($part->disposition) ? strtolower($part->disposition) : null;

            $isAttachment = ($disposition === 'attachment')
                || ($disposition === 'inline' && $filename)
                || (!$disposition && $filename && $part->type != 0 && $part->type != 1);

            if ($isAttachment && $filename) {
                $attachments[] = [
                    'part_number' => $childPartNum,
                    'filename' => $filename,
                    'encoding' => $part->encoding,
                ];
            }

            if (isset($part->parts)) {
                $attachments = array_merge($attachments, $this->findAttachmentParts($part, $childPartNum));
            }
        }

        return $attachments;
    }

    protected function getPartFilename($part)
    {
        foreach (['parameters', 'dparameters'] as $paramSet) {
            if (isset($part->{$paramSet})) {
                foreach ($part->{$paramSet} as $param) {
                    if (in_array(strtolower($param->attribute), ['name', 'filename'])) {
                        return $param->value;
                    }
                }
            }
        }
        return null;
    }

    protected function saveAttachments($inbox, $emailNumber, array $attachmentParts, $ticketId, $replyId = null)
    {
        $fileService = app(FileService::class);
        $saved = [];

        foreach ($attachmentParts as $part) {
            $data = imap_fetchbody($inbox, $emailNumber, $part['part_number']);
            $decoded = $this->decodePart($data, $part['encoding']);

            if (!$decoded) {
                Log::warning("Empty attachment data for {$part['filename']} on ticket #{$ticketId}");
                continue;
            }

            $tempPath = tempnam(sys_get_temp_dir(), 'ticket_');
            file_put_contents($tempPath, $decoded);

            try {
                $uploadedFile = new UploadedFile($tempPath, $part['filename'], null, null, true);
                $uploaded = $fileService->upload($uploadedFile, 'ticket');

                TicketAttachment::create([
                    'ticketid' => $ticketId,
                    'replyid' => $replyId,
                    'file_name' => $uploaded['file_name'],
                    'filetype' => $uploaded['filetype'],
                    'dateadded' => now(),
                ]);

                $saved[] = $uploaded['original_file_name'];
            } catch (\Exception $e) {
                Log::error("Failed to save IMAP attachment {$part['filename']} for ticket #{$ticketId}: " . $e->getMessage());
            } finally {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }
        }

        if ($saved) {
            Log::info("Saved " . count($saved) . " attachment(s) for ticket #{$ticketId}: " . implode(', ', $saved));
        }

        return $saved;
    }
}
