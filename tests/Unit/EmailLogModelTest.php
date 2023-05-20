<?php
//declare(strict_types=1);

namespace Dmcbrn\LaravelEmailDatabaseLog\Test\Unit;

use Dmcbrn\LaravelEmailDatabaseLog\EmailLog;
use Dmcbrn\LaravelEmailDatabaseLog\Test\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailLogModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_model_has_cast_to_datetime(): void
    {
        $emailLog = EmailLog::create(
            [
                'date' => now(),
                'from' => 'Example <hello@example.com>',
                'to' => 'email@example.com',
                'cc' => null,
                'bcc' => null,
                'subject' => 'The e-mail subject',
                'body' => '<p>Some random string.</p>',
                'attachments' => null,
                'messageId' => '12345',
            ]
        );

        $modelCasts = $emailLog->getCasts();

        $this->assertEquals('datetime', $modelCasts['date']);
    }
}