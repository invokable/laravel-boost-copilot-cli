<?php

declare(strict_types=1);

namespace Revolution\Laravel\Boost;

use Illuminate\Support\Str;
use Laravel\Boost\Contracts\SupportsGuidelines;
use Laravel\Boost\Contracts\SupportsMcp;
use Laravel\Boost\Contracts\SupportsSkills;
use Laravel\Boost\Install\Agents\Agent;
use Laravel\Boost\Install\Enums\Platform;

class CopilotCli extends Agent implements SupportsGuidelines, SupportsMcp, SupportsSkills
{
    protected static bool $fake = false;

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
            'command' => 'command -v copilot',
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
        return config('boost.agents.copilot_cli.guidelines_path', '.github/instructions/laravel-boost.instructions.md');
    }

    /**
     * Get the file path where agent skills should be written.
     */
    public function skillsPath(): string
    {
        return config('boost.agents.copilot_cli.skills_path', '.github/skills');
    }

    public function mcpConfigPath(): string
    {
        return '.github/mcp-config.json';
    }

    /**
     * Build the MCP server configuration payload for file-based installation.
     *
     * @param  array<int, string>  $args
     * @param  array<string, string>  $env
     * @return array<string, mixed>
     */
    public function mcpServerConfig(string $command, array $args = [], array $env = []): array
    {
        return [
            'type' => 'local',
            'command' => $this->convertCommandToPhpPath($command),
            'args' => array_values(array_filter([
                ! $this->isRunningInTestbench() ? 'artisan' : false,
                'boost:mcp',
            ])),
            'env' => $env,
            'tools' => ['*'],
        ];
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

    public function isRunningInTestbench(): bool
    {
        if (static::$fake) {
            return true;
        }

        return defined('TESTBENCH_CORE');
    }

    /**
     * Indicates if the Copilot CLI is being faked testbench for testing purposes.
     */
    public static function fake(bool $fake = true): void
    {
        static::$fake = $fake;
    }
}
