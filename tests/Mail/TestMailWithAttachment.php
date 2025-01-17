<?php
declare(strict_types=1);

namespace Dmcbrn\LaravelEmailDatabaseLog\Test\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMailWithAttachment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('The e-mail subject')
            ->html('<p>Some random string.</p>')
            ->attach(__DIR__ . '/../files/attachment.txt');
    }
}
