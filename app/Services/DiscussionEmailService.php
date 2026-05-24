<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionComment;
use App\Models\Staff;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DiscussionEmailService
{
    public function sendCommentNotification(ProjectDiscussion $discussion, ProjectDiscussionComment $comment, Staff $recipient): void
    {
        $project = $discussion->project;
        $author = $comment->staff->full_name ?? 'Someone';

        $subject = "Re: [{$project->name}] {$discussion->subject}";
        $messageBody = view('emails.discussion-comment', [
            'author' => $author,
            'discussion' => $discussion,
            'comment' => $comment,
            'project' => $project,
            'recipient' => $recipient,
        ])->render();

        $replyToEmail = $this->getReplyToAddress($discussion);

        try {
            Mail::send([], [], function ($mail) use ($recipient, $subject, $messageBody, $replyToEmail) {
                $mail->to($recipient->email, $recipient->full_name)
                    ->subject($subject)
                    ->html($messageBody);
                if ($replyToEmail) {
                    $mail->replyTo($replyToEmail);
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to send discussion email notification: ' . $e->getMessage());
        }
    }

    public function processIncomingEmail(string $mboxPath, string $username, string $password): array
    {
        $processed = 0;
        $errors = 0;

        if (!function_exists('imap_open')) {
            Log::error('Discussion email: PHP IMAP extension is not installed.');
            return ['processed' => 0, 'errors' => 1, 'message' => 'PHP IMAP extension required'];
        }

        $mbox = @imap_open($mboxPath, $username, $password);
        if (!$mbox) {
            Log::error('Discussion email: Failed to connect to IMAP: ' . imap_last_error());
            return ['processed' => 0, 'errors' => 1, 'message' => 'IMAP connection failed'];
        }

        $emails = imap_search($mbox, 'UNSEEN');
        if (!$emails) {
            imap_close($mbox);
            return ['processed' => 0, 'errors' => 0, 'message' => 'No unseen emails'];
        }

        foreach ($emails as $emailNo) {
            try {
                $header = imap_headerinfo($mbox, $emailNo);
                $body = imap_fetchbody($mbox, $emailNo, 1);
                $subject = $header->subject ?? '';

                $discussion = $this->extractDiscussionFromSubject($subject);
                if (!$discussion) {
                    imap_mail_move($mbox, $emailNo, 'INBOX');
                    continue;
                }

                $fromEmail = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                $staff = Staff::where('email', $fromEmail)->first();
                if (!$staff) {
                    imap_mail_move($mbox, $emailNo, 'INBOX');
                    continue;
                }

                $content = $this->cleanReplyBody($body);

                $comment = ProjectDiscussionComment::create([
                    'discussion_id' => $discussion->id,
                    'discussion_type' => 'discussion',
                    'content' => $content ?: '(No text content)',
                    'staff_id' => $staff->staffid,
                    'created' => now(),
                ]);

                $discussion->update(['last_activity' => now()]);

                imap_mail_move($mbox, $emailNo, 'INBOX.processed');
                $processed++;
            } catch (\Exception $e) {
                Log::error('Discussion email processing error: ' . $e->getMessage());
                $errors++;
            }
        }

        imap_expunge($mbox);
        imap_close($mbox);

        return ['processed' => $processed, 'errors' => $errors, 'message' => "Processed {$processed}, errors {$errors}"];
    }

    protected function extractDiscussionFromSubject(string $subject): ?ProjectDiscussion
    {
        preg_match('/\[DISC-(\d+)\]/', $subject, $matches);
        if (isset($matches[1])) {
            return ProjectDiscussion::find($matches[1]);
        }
        return null;
    }

    protected function cleanReplyBody(string $body): string
    {
        $body = quoted_printable_decode($body);
        $body = preg_replace('/<[^>]*>/', '', $body);
        $lines = explode("\n", $body);
        $clean = [];
        foreach ($lines as $line) {
            if (preg_match('/^>/', $line)) continue;
            if (preg_match('/^On.*wrote:$/', trim($line))) continue;
            if (preg_match('/^--\s*$/', trim($line))) break;
            if (preg_match('/^_{10,}/', trim($line))) break;
            $clean[] = $line;
        }
        return trim(implode("\n", $clean));
    }

    protected function getReplyToAddress(ProjectDiscussion $discussion): ?string
    {
        $domain = config('mail.discussion_reply_domain', env('MAIL_DISCUSSION_REPLY_DOMAIN'));
        if (!$domain) return null;
        return "discussion+{$discussion->id}@{$domain}";
    }

    public static function isEnabled(): bool
    {
        return (bool) \App\Helpers\SettingsHelper::get('discussion_email_integration_enabled', false);
    }
}
