<?php

namespace Dmcbrn\LaravelEmailDatabaseLog;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;
use Dmcbrn\LaravelEmailDatabaseLog\LaravelEvents\EmailLogged;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Email;

class EmailSymfonyLogger
{
    /**
     * Handle the event.
     *
     * @param MessageSending $event
     */
    public function handle(MessageSending $event)
    {
        $message = $event->message;

        $messageId = Str::uuid();

        $attachments = [];
        foreach ($message->getAttachments() as $dataPart) {
            $attachmentPath = $messageId . '/' . $dataPart->getFilename();
            Storage::disk(config('email_log.disk'))->put($attachmentPath, $dataPart->getBody());
            $attachments[] = $attachmentPath;
		}

        $emailLog = EmailLog::create([
            'date' => date('Y-m-d H:i:s'),
            'from' => $this->formatAddressField($message, 'From'),
            'to' => $this->formatAddressField($message, 'To'),
            'cc' => $this->formatAddressField($message, 'Cc'),
            'bcc' => $this->formatAddressField($message, 'Bcc'),
            'subject' => $message->getSubject(),
            'body' => $message->getBody()->bodyToString(),
            'headers' => $message->getHeaders()->toString(),
            'attachments' => empty($attachments) ? null : implode(', ', $attachments),
            'messageId' => $messageId,
            'mail_driver' => config('mail.driver'),
        ]);

        event(new EmailLogged($emailLog));
    }

    /**
     * Format address strings for sender, to, cc, bcc.
     *
     * @param Email $message
     * @param string $field
     * @return null|string
     */
    function formatAddressField(Email $message, string $field): ?string
    {
        $headers = $message->getHeaders();

        return $headers->get($field)?->getBodyAsString();
    }
}
