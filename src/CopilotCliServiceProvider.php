<?php

declare(strict_types=1);

namespace Revolution\Laravel\Boost;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Boost\Boost;

use function Orchestra\Testbench\default_skeleton_path;

class CopilotCliServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Boost::registerCodeEnvironment('copilot-cli', CopilotCli::class);

        Event::listen(function (CommandStarting $event) {
            if (! defined('TESTBENCH_CORE')) {
                return;
            }

            if (in_array($event->command, ['boost:install', 'boost:update'], true)) {
                $this->app->setBasePath(realpath(__DIR__.'/..'));
                $this->app->useStoragePath(default_skeleton_path('storage'));
                $this->app->useAppPath(default_skeleton_path('app'));
            }
        });
    }
}
