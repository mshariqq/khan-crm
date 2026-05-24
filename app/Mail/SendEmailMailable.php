<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $viewData;
    public $fromEmail;
    public $fromName;
    public $replyToAddress;

    public function __construct(
        string $subject,
        string $body,
        ?string $fromEmail = null,
        ?string $fromName = null,
        ?string $replyToAddress = null,
        ?string $logo = null,
        ?string $companyName = null,
        ?string $companyAddress = null,
    ) {
        $this->viewData = [
            'subject' => $subject,
            'body' => $body,
            'logo' => $logo,
            'company_name' => $companyName,
            'company_address' => $companyAddress,
        ];
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->replyToAddress = $replyToAddress;
    }

    public function build()
    {
        $mail = $this->subject($this->viewData['subject'])
            ->view('emails.base', $this->viewData);

        if ($this->fromEmail) {
            $mail->from($this->fromEmail, $this->fromName ?? $this->viewData['company_name'] ?? config('app.name'));
        }

        if ($this->replyToAddress) {
            $mail->replyTo($this->replyToAddress);
        }

        return $mail;
    }
}
