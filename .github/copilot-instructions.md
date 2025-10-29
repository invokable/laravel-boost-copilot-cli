# Laravel Boost Copilot CLI - Project Guidelines

## Project Overview

This is a Laravel package that provides custom CodeEnvironment integration for GitHub Copilot CLI with Laravel Boost. It enables Laravel projects to use GitHub Copilot CLI with Laravel Boost's MCP (Model Context Protocol) server functionality.

## Technology Stack

- **Language**: PHP 8.3+
- **Framework**: Laravel 12.x+
- **Dependencies**: Laravel Boost 1.6+
- **Target Platforms**: macOS, WSL/Ubuntu, Linux (Windows not supported)

## Architecture

### Core Components

1. **CopilotCli.php**: Main CodeEnvironment implementation
   - Implements `Agent` and `McpClient` interfaces
   - Handles detection, configuration, and MCP installation
   - Generates `.github/copilot-instructions.md` and `.github/mcp-config.json`

2. **CopilotCliServiceProvider.php**: Laravel service provider
   - Registers the CopilotCli CodeEnvironment with Laravel Boost
   - Auto-discovered via Laravel's package discovery

### Key Features

- System-wide and project-specific detection
- MCP server configuration for Copilot CLI
- File-based MCP installation strategy
- Integration with `php artisan boost:install` command

## Code Style Guidelines

### PHP Standards
- Follow PSR-12 coding standards
- Use strict types declaration
- Use return type declarations
- Follow Laravel conventions and best practices

### Namespace Convention
- Root namespace: `Revolution\Laravel\Boost`
- Follow PSR-4 autoloading standards

## Development Guidelines

### When Making Changes

1. **Service Provider**: Only modify for registration logic
2. **CopilotCli Class**: 
   - Keep detection configs simple and reliable
   - Maintain compatibility with Laravel Boost interfaces
   - Ensure JSON configuration format is valid
   - Use `File` facade for file operations

3. **Configuration Files**:
   - `.github/copilot-instructions.md`: AI guidelines path
   - `.github/mcp-config.json`: MCP server configuration

### Testing Approach
- Test with `php artisan boost:install` command
- Verify file generation in `.github/` directory
- Test with actual Copilot CLI: `copilot --additional-mcp-config @.github/mcp-config.json`
- Confirm "Configured MCP servers: laravel-boost" appears

## Package Integration

### Laravel Boost Integration Points
- Extends `CodeEnvironment` base class
- Implements `Agent` interface for IDE/tool detection
- Implements `McpClient` interface for MCP server setup
- Uses `McpInstallationStrategy::FILE` for config file generation

### MCP Configuration Format
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

## Important Constraints

1. **Platform Support**: Do NOT add Windows native support (only WSL/Ubuntu on Windows)
2. **PHP Version**: Minimum PHP 8.3
3. **Laravel Version**: Minimum Laravel 12.x
4. **File Structure**: Keep configuration files in `.github/` directory
5. **MCP Strategy**: Always use FILE strategy, not REGISTRY

## Common Tasks

### Adding New Detection Methods
- Update `systemDetectionConfig()` or `projectDetectionConfig()`
- Keep detection lightweight and fast

### Modifying MCP Configuration
- Edit `installFileMcp()` method
- Ensure backward compatibility with existing configs
- Validate JSON format before writing

### Updating Documentation
- Update README.md for user-facing changes
- Keep installation instructions clear and concise
- Include version requirements

## Dependencies Management

- Keep dependencies minimal
- Only depend on Laravel core packages and Laravel Boost
- Development-only package (require-dev in user projects)

## Release Notes

When preparing releases, ensure:
- Compatibility with Laravel Boost version requirements
- Test with latest Copilot CLI version
- Update README.md if installation steps change
- Follow semantic versioning
