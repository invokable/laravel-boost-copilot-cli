<?php

declare(strict_types=1);

namespace Revolution\Laravel\Boost;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;
use Laravel\Boost\Contracts\Agent;
use Laravel\Boost\Contracts\McpClient;
use Laravel\Boost\Install\CodeEnvironment\CodeEnvironment;
use Laravel\Boost\Install\Enums\Platform;

class CopilotCli extends CodeEnvironment implements Agent, McpClient
{
    public function name(): string
    {
        return 'copilot-cli';
    }

    public function displayName(): string
    {
        return 'GitHub Copilot CLI';
    }

    /**
     * Get the detection configuration for system-wide installation detection.
     *
     * @return array{paths?: string[], command?: string, files?: string[]}
     */
    public function systemDetectionConfig(Platform $platform): array
    {
        return match ($platform) {
            Platform::Darwin, Platform::Linux => [
                'command' => 'command -v copilot',
            ],
            Platform::Windows => [
                'command' => 'where copilot 2>nul',
            ],
        };
    }

    /**
     * Get the detection configuration for project-specific detection.
     *
     * @return array{paths?: string[], files?: string[]}
     */
    public function projectDetectionConfig(): array
    {
        return [
            'paths' => ['.github/instructions'],
            'files' => ['.github/copilot-instructions.md', '.github/instructions/laravel-boost.instructions.md', 'AGENTS.md', 'CLAUDE.md', 'GEMINI.md'],
        ];
    }

    /**
     * Get the display name of the Agent.
     */
    public function agentName(): ?string
    {
        return 'GitHub Copilot(Custom instructions)';
    }

    /**
     * Get the file path where AI guidelines should be written.
     *
     * @return string The relative or absolute path to the guideline file
     */
    public function guidelinesPath(): string
    {
        return '.github/instructions/laravel-boost.instructions.md';
    }

    public function mcpConfigPath(): string
    {
        return '.github/mcp-config.json';
    }

    /**
     * Convert command to appropriate PHP path for MCP configuration.
     */
    public function convertCommandToPhpPath(string $command): string
    {
        if ($this->isRunningInTestbench()) {
            return './vendor/bin/testbench';
        }

        return match (Str::afterLast($command, '/')) {
            'wsl', 'wsl.exe' => 'php',
            'sail' => './vendor/bin/sail',
            default => $command,
        };
    }

    /**
     * Install MCP server with GitHub Copilot CLI specific configuration.
     *
     * @param  array<int, string>  $args
     * @param  array<string, string>  $env
     *
     * @throws FileNotFoundException
     * @throws JsonException
     */
    protected function installFileMcp(string $key, string $command, array $args = [], array $env = []): bool
    {
        $path = $this->mcpConfigPath();
        if (! $path) {
            return false;
        }

        File::ensureDirectoryExists(dirname($path));

        $config = [];
        if (File::exists($path)) {
            $existingContent = File::get($path);
            $config = json_decode($existingContent, true, 512, JSON_THROW_ON_ERROR) ?? [];
        }

        $phpPath = $this->convertCommandToPhpPath($command);

        // Build server configuration with type and tools fields
        $serverConfig = [
            'type' => 'local',
            'command' => $phpPath,
            'args' => array_values(array_filter([
                ! $this->isRunningInTestbench() ? 'artisan' : false,
                'boost:mcp',
            ])),
            'tools' => ['*'],
        ];

        if (! empty($env)) {
            $serverConfig['env'] = $env;
        }

        data_set($config, $this->mcpConfigKey().'.'.$key, $serverConfig);

        // Remove empty arrays from existing config to avoid compatibility issues
        $config = $this->removeEmptyArrays($config);
        $json = json_encode($config, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json) {
            $json = str_replace("\r\n", "\n", $json);

            return File::put($path, $json) !== false;
        }

        return false;
    }

    /**
     * Recursively remove empty arrays from config to avoid compatibility issues.
     * Some MCP tools fail when encountering empty arrays (e.g., "headers": []).
     */
    protected function removeEmptyArrays(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (empty($value)) {
                    unset($data[$key]);
                } else {
                    $data[$key] = $this->removeEmptyArrays($value);
                }
            }
        }

        return $data;
    }

    protected function isRunningInTestbench(): bool
    {
        return defined('TESTBENCH_CORE');
    }
}
