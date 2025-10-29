<?php

declare(strict_types=1);

use Laravel\Boost\Boost;
use Revolution\Laravel\Boost\CopilotCli;

test('CopilotCliServiceProvider registers code environment', function (): void {
    $environments = Boost::getCodeEnvironments();

    expect($environments)->toHaveKey('copilot-cli')
        ->and($environments['copilot-cli'])->toBe(CopilotCli::class);
});
