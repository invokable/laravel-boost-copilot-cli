<?php

declare(strict_types=1);

namespace Revolution\Laravel\Boost;

use Laravel\Boost\Boost;
use Illuminate\Support\ServiceProvider;

class CopilotCliServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Boost::registerCodeEnvironment('copilot-cli', CopilotCli::class);
    }
}
