<?php

declare(strict_types=1);

use Laravel\Boost\Install\Detection\DetectionStrategyFactory;
use Revolution\Laravel\Boost\CopilotCli;

test('CopilotCli returns correct name', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->name())->toBe('copilot-cli');
});

test('CopilotCli returns correct display name', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->displayName())->toBe('GitHub Copilot CLI');
});

test('CopilotCli returns correct MCP config path', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->mcpConfigPath())->toBe('.github/mcp-config.json');
});

test('CopilotCli system detection config uses "command -v" command', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    $config = $copilotCli->systemDetectionConfig(\Laravel\Boost\Install\Enums\Platform::Darwin);

    expect($config)->toHaveKey('command')
        ->and($config['command'])->toBe('command -v copilot');
});

test('CopilotCli project detection config checks for copilot-instructions.md', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    $config = $copilotCli->projectDetectionConfig();

    expect($config)->toHaveKey('files')
        ->and($config['files'])->toContain('.github/copilot-instructions.md');
});

test('CopilotCli uses FILE installation strategy', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->mcpInstallationStrategy())
        ->toBe(\Laravel\Boost\Install\Enums\McpInstallationStrategy::FILE);
});

test('CopilotCli mcpServerConfig returns correct structure', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    $config = $copilotCli->mcpServerConfig('php', ['artisan', 'boost:mcp']);

    expect($config)->toMatchArray([
        'type' => 'local',
        'command' => 'php',
        'args' => ['artisan', 'boost:mcp'],
        'tools' => ['*'],
    ]);
});

test('CopilotCli converts wsl command to php', function (): void {
    CopilotCli::fake(testbench: false, wsl: true);
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->convertCommandToPhpPath('wsl'))->toBe('php')
        ->and($copilotCli->convertCommandToPhpPath('wsl.exe'))->toBe('php')
        ->and($copilotCli->convertCommandToPhpPath('/usr/bin/wsl'))->toBe('php');

    CopilotCli::fake(testbench: false, wsl: false);
});

test('CopilotCli converts sail command to vendor/bin/sail', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->convertCommandToPhpPath('./vendor/bin/sail'))->toBe('./vendor/bin/sail')
        ->and($copilotCli->convertCommandToPhpPath('/home/user/project/vendor/bin/sail'))->toBe('./vendor/bin/sail');
});

test('CopilotCli uses other commands as-is', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->convertCommandToPhpPath('/usr/bin/php8.3'))->toBe('/usr/bin/php8.3')
        ->and($copilotCli->convertCommandToPhpPath('php'))->toBe('php');
});

test('CopilotCli detects testbench environment when TESTBENCH_CORE is defined', function (): void {
    CopilotCli::fake();

    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    $result = $copilotCli->isRunningInTestbench();
    expect($result)->toBeTrue();
});

test('CopilotCli converts command to testbench when TESTBENCH_CORE is defined', function (): void {
    CopilotCli::fake();

    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    // When running in testbench (TESTBENCH_CORE defined), it should return ./vendor/bin/testbench
    expect($copilotCli->convertCommandToPhpPath('php'))->toBe('./vendor/bin/testbench')
        ->and($copilotCli->convertCommandToPhpPath('wsl'))->toBe('./vendor/bin/testbench')
        ->and($copilotCli->convertCommandToPhpPath('./vendor/bin/sail'))->toBe('./vendor/bin/testbench');
});

test('CopilotCli mcpServerConfig uses testbench settings when in testbench', function (): void {
    CopilotCli::fake();

    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    $config = $copilotCli->mcpServerConfig('php');

    expect($config)->toMatchArray([
        'type' => 'local',
        'command' => './vendor/bin/testbench',
        'args' => ['boost:mcp'],
        'tools' => ['*'],
    ]);
});
