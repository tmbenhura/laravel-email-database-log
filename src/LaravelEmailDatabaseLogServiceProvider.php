<?php

namespace Dmcbrn\LaravelEmailDatabaseLog;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;

class LaravelEmailDatabaseLogServiceProvider extends ServiceProvider
{

    public function register()
    {
//        $this->app->make('Dmcbrn\LaravelEmailDatabaseLog\EmailLogController');

        $this->app['events']->listen(
            MessageSending::class,
            intval(Str::before($this->app->version(), '.')) > 8
                ? EmailSymfonyLogger::class
                : EmailSwiftLogger::class
        );

        $this->loadViewsFrom(__DIR__ . '/../views','email-logger');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/email_log.php', 'email_log'
        );
    }

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__ . '/../config/email_log.php' => config_path('email_log.php'),
        ]);
    }
}
