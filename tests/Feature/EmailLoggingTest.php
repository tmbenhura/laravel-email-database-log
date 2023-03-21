<?php
//declare(strict_types=1);

namespace Dmcbrn\LaravelEmailDatabaseLog\Test\Feature;

use Dmcbrn\LaravelEmailDatabaseLog\EmailLog;
use Dmcbrn\LaravelEmailDatabaseLog\Test\Mail\TestMail;
use Dmcbrn\LaravelEmailDatabaseLog\Test\Mail\TestMailWithAttachment;
use Dmcbrn\LaravelEmailDatabaseLog\Test\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\Concerns\CreatesApplication;

class EmailLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Freeze time for consistent date tests
        Carbon::setTestNow(Carbon::now());
    }

    public function test_emails_are_logged_to_the_database(): void
    {
        Mail::to('email@example.com')
            ->send(new TestMail());

        $this->assertDatabaseHas(
            'email_log',
            [
                'date' => now()->format('Y-m-d H:i:s'),
                'from' => 'Example <hello@example.com>',
                'to' => 'email@example.com',
                'cc' => null,
                'bcc' => null,
                'subject' => 'The e-mail subject',
                'body' => '<p>Some random string.</p>',
                'attachments' => null,
            ]
        );
    }

    public function test_multiple_recipients_are_comma_separated(): void
    {
        Mail::to(['email@example.com', 'email2@example.com'])
            ->send(new TestMail());

        $this->assertDatabaseHas(
            'email_log',
            [
                'date' => now()->format('Y-m-d H:i:s'),
                'to' => 'email@example.com, email2@example.com',
                'cc' => null,
                'bcc' => null,
            ]
        );
    }

    public function test_recipient_with_name_is_correctly_formatted(): void
    {
        Mail::to((object)['email' => 'email@example.com', 'name' => 'John Doe'])
            ->send(new TestMail());

        $this->assertDatabaseHas(
            'email_log',
            [
                'date' => now()->format('Y-m-d H:i:s'),
                'to' => 'John Doe <email@example.com>',
                'cc' => null,
                'bcc' => null,
            ]
        );
    }

    public function test_cc_recipient_with_name_is_correctly_formatted(): void
    {
        Mail::cc((object)['email' => 'email@example.com', 'name' => 'John Doe'])
            ->send(new TestMail());

        $this->assertDatabaseHas(
            'email_log',
            [
                'date' => now()->format('Y-m-d H:i:s'),
                'to' => null,
                'cc' => 'John Doe <email@example.com>',
                'bcc' => null,
            ]
        );
    }

    public function test_bcc_recipient_with_name_is_correctly_formatted(): void
    {
        Mail::bcc((object)['email' => 'email@example.com', 'name' => 'John Doe'])
            ->send(new TestMail());

        $this->assertDatabaseHas(
            'email_log',
            [
                'date' => now()->format('Y-m-d H:i:s'),
                'to' => null,
                'cc' => null,
                'bcc' => 'John Doe <email@example.com>',
            ]
        );
    }

    public function test_email_attachment_is_saved_to_disk(): void
    {
        Mail::to('email@example.com')->send(new TestMailWithAttachment());

        $log = EmailLog::first();

        $originalAttachment = file_get_contents(__DIR__ . '/../files/attachment.txt');
        $savedAttachment = Storage::disk(config('email_log.disk'))->get($log->attachments);

        $this->assertEquals($originalAttachment, $savedAttachment);
    }
}