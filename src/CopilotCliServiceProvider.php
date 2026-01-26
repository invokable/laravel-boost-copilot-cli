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
        Boost::registerAgent('copilot-cli', CopilotCli::class);

        if (defined('TESTBENCH_CORE')) {
            $this->testbench();
        }
    }

    /**
     * Fixed a path issue when running testbench.
     */
    protected function testbench(): void
    {
        Event::listen(function (CommandStarting $event) {
            if (! in_array($event->command, ['boost:install', 'boost:update'], true)) {
                return;
            }

            // Change the base path from the testbench skeleton to the current working directory.
            $this->app->setBasePath(getcwd());

            $this->app->useStoragePath(default_skeleton_path('storage'));
            $this->app->useAppPath(default_skeleton_path('app'));
            // Add any necessary paths other than storage and app.
        });
    }
}
