<?php

declare(strict_types=1);

namespace Revolution\Laravel\Boost;

use Illuminate\Support\Facades\File;
use Laravel\Boost\Contracts\Agent;
use Laravel\Boost\Contracts\McpClient;
use Laravel\Boost\Install\CodeEnvironment\CodeEnvironment;
use Laravel\Boost\Install\Enums\McpInstallationStrategy;
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
        return [
            'command' => 'which copilot',
        ];
    }

    /**
     * Get the detection configuration for project-specific detection.
     *
     * @return array{paths?: string[], files?: string[]}
     */
    public function projectDetectionConfig(): array
    {
        return [
            'files' => ['.github/copilot-instructions.md'],
        ];
    }

    /**
     * Get the file path where AI guidelines should be written.
     *
     * @return string The relative or absolute path to the guideline file
     */
    public function guidelinesPath(): string
    {
        return '.github/copilot-instructions.md';
    }

    public function mcpConfigPath(): string
    {
        return '.github/mcp-config.json';
    }

    public function mcpInstallationStrategy(): McpInstallationStrategy
    {
        return McpInstallationStrategy::FILE;
    }

    /**
     * Install MCP server with GitHub Copilot CLI specific configuration.
     *
     * @param  array<int, string>  $args
     * @param  array<string, string>  $env
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
            $config = json_decode($existingContent, true) ?? [];
        }

        // Build server configuration with type and tools fields
        // Use fixed values for GitHub Copilot CLI
        $serverConfig = [
            'type' => 'local',
            'command' => 'php',
            'args' => [
                'artisan',
                'boost:mcp',
            ],
            'tools' => ['*'],
        ];

        if (! empty($env)) {
            $serverConfig['env'] = $env;
        }

        data_set($config, $this->mcpConfigKey().'.'.$key, $serverConfig);

        // Remove empty arrays from existing config to avoid compatibility issues
        $config = $this->removeEmptyArrays($config);
        $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
}
