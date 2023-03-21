<?php
declare(strict_types=1);

namespace Dmcbrn\LaravelEmailDatabaseLog\Test;

use Dmcbrn\LaravelEmailDatabaseLog\LaravelEmailDatabaseLogServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelEmailDatabaseLogServiceProvider::class,
        ];
    }
}