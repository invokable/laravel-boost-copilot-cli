<?php

declare(strict_types=1);

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Revolution\Laravel\Boost\CopilotCliServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function defineEnvironment($app): void
    {
        $app['env'] = 'testing';
    }

    protected function getPackageProviders($app): array
    {
        return [CopilotCliServiceProvider::class];
    }
}
