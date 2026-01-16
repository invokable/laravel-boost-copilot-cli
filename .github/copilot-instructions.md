# Laravel Boost Copilot CLI - Project Guidelines

## Project Overview

This is a Laravel package that provides custom CodeEnvironment integration for GitHub Copilot CLI with Laravel Boost. It enables Laravel projects to use GitHub Copilot CLI with Laravel Boost's MCP (Model Context Protocol) server functionality.

## Technology Stack

- **Language**: PHP 8.2+
- **Framework**: Laravel 12.x+
- **Dependencies**: Laravel Boost 1.8+
- **Target Platforms**: macOS, WSL, Linux (Native Windows not supported)
- **Testing**: Pest PHP 4.x
- **Code Quality**: Laravel Pint (PSR-12)

## Commands
```bash
composer run test          # Run all tests
composer run lint          # Format code with Pint
composer run test:lint     # Check code formatting
```

## Architecture

### Core Components

1. **CopilotCli.php**: Main CodeEnvironment implementation
   - Implements `Agent` `McpClient` interfaces
   - Handles detection, configuration, and MCP installation

2. **CopilotCliServiceProvider.php**: Laravel service provider
   - Registers the CopilotCli CodeEnvironment with Laravel Boost

### Configuration Files
- `.github/instructions/laravel-boost.instructions.md`: AI guidelines path
- `.github/mcp-config.json`: MCP server configuration

## MCP Configuration Format

`type` and `tools` are required.

### Normal Laravel Application
```json
{
  "mcpServers": {
    "laravel-boost": {
      "type": "local",
      "command": "php",
      "args": ["artisan", "boost:mcp"],
      "tools": ["*"]
    }
  }
}
```

### Laravel Sail
```json
{
    "mcpServers": {
        "laravel-boost": {
            "type": "local",
            "command": "./vendor/bin/sail",
            "args": [
                "artisan",
                "boost:mcp"
            ],
            "tools": [
                "*"
            ]
        }
    }
}
```

### Testbench
```json
{
    "mcpServers": {
        "laravel-boost": {
            "type": "local",
            "command": "./vendor/bin/testbench",
            "args": [
                "boost:mcp"
            ],
            "tools": [
                "*"
            ]
        }
    }
}
```

## AI Guidelines template

- `resources/boost/guidelines/core.blade.php` is used as a third-party AI guideline when users of this package run `php artisan boost:install`.
- `.ai/guidelines/copilot-cli.blade.php` is for the development of this package itself. It is added to the guidelines by `vendor/bin/testbench boost:install`.
- The contents are the same, so they are automatically copied using `.github/workflows/copy-guideline.yml`
- No need to modify `copilot-cli.blade.php`.
