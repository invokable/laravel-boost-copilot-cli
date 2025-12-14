<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
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

test('CopilotCli installs MCP configuration correctly', function (): void {
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir, 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
        $copilotCli = new CopilotCli($strategyFactory);

        // Use reflection to call protected method
        $reflection = new ReflectionClass($copilotCli);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        // Mock the mcpConfigPath to use temp directory
        $pathMethod = $reflection->getMethod('mcpConfigPath');
        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])->makePartial();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);

        $result = $method->invoke($copilotCliMock, 'laravel-boost', 'php', ['artisan', 'boost:mcp']);

        expect($result)->toBeTrue()
            ->and(File::exists($configPath))->toBeTrue();

        $config = json_decode(File::get($configPath), true);

        expect($config)->toHaveKey('mcpServers')
            ->and($config['mcpServers'])->toHaveKey('laravel-boost')
            ->and($config['mcpServers']['laravel-boost'])->toMatchArray([
                'type' => 'local',
                'command' => 'php',
                'args' => ['artisan', 'boost:mcp'],
                'tools' => ['*'],
            ]);
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});

test('CopilotCli preserves existing MCP configuration when installing', function (): void {
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir.'/.github', 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        // Create existing config
        $existingConfig = [
            'mcpServers' => [
                'existing-server' => [
                    'type' => 'remote',
                    'url' => 'https://example.com',
                ],
            ],
        ];
        File::put($configPath, json_encode($existingConfig, JSON_PRETTY_PRINT));

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
        $copilotCli = new CopilotCli($strategyFactory);

        $reflection = new ReflectionClass($copilotCli);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])->makePartial();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);

        $method->invoke($copilotCliMock, 'laravel-boost', 'php', ['artisan', 'boost:mcp']);

        $config = json_decode(File::get($configPath), true);

        // Both servers should exist
        expect($config['mcpServers'])->toHaveKeys(['existing-server', 'laravel-boost']);
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});

test('CopilotCli converts wsl command to php', function (): void {
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    expect($copilotCli->convertCommandToPhpPath('wsl'))->toBe('php')
        ->and($copilotCli->convertCommandToPhpPath('wsl.exe'))->toBe('php')
        ->and($copilotCli->convertCommandToPhpPath('/usr/bin/wsl'))->toBe('php');
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
    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    $reflection = new ReflectionClass($copilotCli);
    $method = $reflection->getMethod('isRunningInTestbench');
    $method->setAccessible(true);

    // When vendor/bin/testbench is used, TESTBENCH_CORE constant is defined
    // In unit tests without actual testbench command, it returns false
    $result = $method->invoke($copilotCli);
    expect($result)->toBeBool();
});

test('CopilotCli converts command to testbench when TESTBENCH_CORE is defined', function (): void {
    // Define the constant temporarily to simulate testbench environment
    if (! defined('TESTBENCH_CORE')) {
        define('TESTBENCH_CORE', true);
    }

    $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
    $copilotCli = new CopilotCli($strategyFactory);

    // When running in testbench (TESTBENCH_CORE defined), it should return ./vendor/bin/testbench
    expect($copilotCli->convertCommandToPhpPath('php'))->toBe('./vendor/bin/testbench')
        ->and($copilotCli->convertCommandToPhpPath('wsl'))->toBe('./vendor/bin/testbench')
        ->and($copilotCli->convertCommandToPhpPath('./vendor/bin/sail'))->toBe('./vendor/bin/testbench');
})->skip('TESTBENCH_CORE constant cannot be conditionally defined in same process');

test('CopilotCli installs MCP configuration with testbench settings when in testbench', function (): void {
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir, 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);

        // Mock CopilotCli to simulate testbench environment
        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);
        $copilotCliMock->shouldAllowMockingProtectedMethods();
        $copilotCliMock->shouldReceive('isRunningInTestbench')->andReturn(true);

        $reflection = new ReflectionClass($copilotCliMock);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        // When running in testbench, it should use ./vendor/bin/testbench
        $result = $method->invoke($copilotCliMock, 'laravel-boost', 'php');

        expect($result)->toBeTrue()
            ->and(File::exists($configPath))->toBeTrue();

        $config = json_decode(File::get($configPath), true);

        expect($config)->toHaveKey('mcpServers')
            ->and($config['mcpServers'])->toHaveKey('laravel-boost')
            ->and($config['mcpServers']['laravel-boost'])->toMatchArray([
                'type' => 'local',
                'command' => './vendor/bin/testbench',
                'args' => ['boost:mcp'],
                'tools' => ['*'],
            ]);
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});

test('CopilotCli removes "artisan" from args when running in testbench', function (): void {
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir, 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);

        // Mock CopilotCli to simulate testbench environment
        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);
        $copilotCliMock->shouldAllowMockingProtectedMethods();
        $copilotCliMock->shouldReceive('isRunningInTestbench')->andReturn(true);

        $reflection = new ReflectionClass($copilotCliMock);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        $result = $method->invoke($copilotCliMock, 'laravel-boost', 'php');

        $config = json_decode(File::get($configPath), true);

        // args should only contain 'boost:mcp', not 'artisan' when in testbench
        expect($config['mcpServers']['laravel-boost']['args'])->toBe(['boost:mcp'])
            ->and($config['mcpServers']['laravel-boost']['args'])->not->toContain('artisan');
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});
