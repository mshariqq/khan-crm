<?php

namespace App\Jobs;

use App\Mail\SendEmailMailable;
use App\Models\EmailTracking;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];

    protected string $to;
    protected string $subject;
    protected string $body;
    protected ?string $fromEmail;
    protected ?string $fromName;
    protected ?string $replyTo;
    protected ?string $slug;
    protected ?string $trackableType;
    protected ?int $trackableId;

    public function __construct(
        string $to,
        string $subject,
        string $body,
        ?string $fromEmail = null,
        ?string $fromName = null,
        ?string $replyTo = null,
        ?string $slug = null,
        ?string $trackableType = null,
        ?int $trackableId = null,
    ) {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->replyTo = $replyTo;
        $this->slug = $slug;
        $this->trackableType = $trackableType;
        $this->trackableId = $trackableId;
    }

    public function handle(): void
    {
        $messageId = Str::uuid() . '@' . config('app.url');

        $tracking = null;
        if ($this->shouldTrack()) {
            $tracking = EmailTracking::create([
                'message_id' => $messageId,
                'recipient_email' => $this->to,
                'subject' => $this->subject,
                'slug' => $this->slug,
                'trackable_type' => $this->trackableType,
                'trackable_id' => $this->trackableId,
                'sent_at' => now(),
                'status' => 'sent',
            ]);
        }

        $body = $this->body;
        if ($tracking) {
            $trackingPixel = '<img src="' . route('email.tracking.open', $tracking->message_id) . '" width="1" height="1" style="display:none" alt="" />';
            $body = str_replace('</body>', $trackingPixel . '</body>', $body);

            $body = preg_replace_callback(
                '/<a\s+([^>]*?)href="([^"]+)"([^>]*)>/i',
                function ($matches) use ($tracking) {
                    $url = $matches[2];
                    $trackingUrl = route('email.tracking.click', [
                        'message_id' => $tracking->message_id,
                        'url' => base64_encode($url),
                    ]);
                    return '<a ' . $matches[1] . 'href="' . $trackingUrl . '"' . $matches[3] . '>';
                },
                $body
            );
        }

        try {
            $mailable = new SendEmailMailable(
                $this->subject,
                $body,
                $this->fromEmail,
                $this->fromName,
                $this->replyTo,
            );

            Mail::to($this->to)->queue($mailable);
        } catch (\Exception $e) {
            Log::error("SendEmailJob failed for {$this->to}: " . $e->getMessage());

            if ($tracking) {
                $tracking->update([
                    'status' => 'failed',
                    'bounce_reason' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("SendEmailJob permanently failed for {$this->to}: " . $e->getMessage());

        EmailTracking::where('recipient_email', $this->to)
            ->where('slug', $this->slug)
            ->where('status', 'sent')
            ->update(['status' => 'failed', 'bounce_reason' => $e->getMessage()]);
    }

    protected function shouldTrack(): bool
    {
        return $this->slug !== null;
    }
}
