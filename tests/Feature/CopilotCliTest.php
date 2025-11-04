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
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir.'/.github', 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
        $copilotCli = new CopilotCli($strategyFactory);

        $reflection = new ReflectionClass($copilotCli);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])->makePartial();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);

        $result = $method->invoke($copilotCliMock, 'laravel-boost', 'wsl', ['artisan', 'boost:mcp']);

        expect($result)->toBeTrue();

        $config = json_decode(File::get($configPath), true);

        expect($config['mcpServers']['laravel-boost']['command'])->toBe('php');
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});

test('CopilotCli converts relative sail path to vendor/bin/sail', function (): void {
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir.'/.github', 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
        $copilotCli = new CopilotCli($strategyFactory);

        $reflection = new ReflectionClass($copilotCli);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])->makePartial();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);

        $result = $method->invoke($copilotCliMock, 'laravel-boost', './vendor/bin/sail', ['artisan', 'boost:mcp']);

        expect($result)->toBeTrue();

        $config = json_decode(File::get($configPath), true);

        expect($config['mcpServers']['laravel-boost']['command'])->toBe('./vendor/bin/sail');
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});

test('CopilotCli converts absolute sail path to vendor/bin/sail', function (): void {
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir.'/.github', 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
        $copilotCli = new CopilotCli($strategyFactory);

        $reflection = new ReflectionClass($copilotCli);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])->makePartial();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);

        $result = $method->invoke($copilotCliMock, 'laravel-boost', '/home/user/project/vendor/bin/sail', ['artisan', 'boost:mcp']);

        expect($result)->toBeTrue();

        $config = json_decode(File::get($configPath), true);

        expect($config['mcpServers']['laravel-boost']['command'])->toBe('./vendor/bin/sail');
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});

test('CopilotCli uses other commands as-is', function (): void {
    $tempDir = sys_get_temp_dir().'/copilot-cli-test-'.uniqid();
    mkdir($tempDir.'/.github', 0777, true);

    try {
        $configPath = $tempDir.'/.github/mcp-config.json';

        $strategyFactory = Mockery::mock(DetectionStrategyFactory::class);
        $copilotCli = new CopilotCli($strategyFactory);

        $reflection = new ReflectionClass($copilotCli);
        $method = $reflection->getMethod('installFileMcp');
        $method->setAccessible(true);

        $copilotCliMock = Mockery::mock(CopilotCli::class, [$strategyFactory])->makePartial();
        $copilotCliMock->shouldReceive('mcpConfigPath')->andReturn($configPath);

        // Test with a custom command
        $result = $method->invoke($copilotCliMock, 'laravel-boost', '/usr/bin/php8.3', ['artisan', 'boost:mcp']);

        expect($result)->toBeTrue();

        $config = json_decode(File::get($configPath), true);

        expect($config['mcpServers']['laravel-boost']['command'])->toBe('/usr/bin/php8.3');
    } finally {
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
});
