<?php

declare(strict_types=1);

use Laravel\Boost\Boost;
use Revolution\Laravel\Boost\CopilotCli;

test('CopilotCliServiceProvider registers code Agent', function (): void {
    $environments = Boost::getAgents();

    expect($environments)->toHaveKey('copilot-cli')
        ->and($environments['copilot-cli'])->toBe(CopilotCli::class);
});
