# Laravel Boost Copilot CLI - Project Guidelines

## Project Overview

This is a Laravel package that provides custom CodeEnvironment integration for GitHub Copilot CLI with Laravel Boost. It enables Laravel projects to use GitHub Copilot CLI with Laravel Boost's MCP (Model Context Protocol) server functionality.

## Technology Stack

- **Language**: PHP 8.3+
- **Framework**: Laravel 12.x+
- **Dependencies**: Laravel Boost 1.6+
- **Target Platforms**: macOS, WSL, Linux (Native Windows not supported)
- **Testing**: Pest PHP 4.x
- **Code Quality**: Laravel Pint (PSR-12)

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
- Follow PSR-12 coding standards (enforced by Laravel Pint)
- Use strict types declaration (`declare(strict_types=1);`)
- Use return type declarations for all methods
- Follow Laravel conventions and best practices
- Run `composer lint` before committing
- Verify formatting with `composer test:lint`

### Namespace Convention
- Root namespace: `Revolution\Laravel\Boost`
- Follow PSR-4 autoloading standards
- Test namespace: `Tests\`

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

#### Automated Tests
- **Framework**: Pest PHP with Orchestra Testbench
- **Run tests**: `composer test` or `vendor/bin/pest`
- **Test coverage**: `vendor/bin/pest --coverage`
- **Test structure**: 
  - `tests/Feature/` - Feature tests for main functionality
  - `tests/TestCase.php` - Base test case with package provider setup
  - `tests/Pest.php` - Pest configuration and architecture presets
  - `tests/ArchTest.php` - Architecture rules and code quality checks

#### Writing Tests
- Use Pest PHP syntax (test functions, not classes)
- Mock `DetectionStrategyFactory` for unit tests
- Test public methods and behavior, not implementation details
- Use descriptive test names: `test('description of expected behavior')`
- Use temporary directories for file system tests and clean up after
- Follow the pattern in existing tests

#### Integration Tests
- Test with `php artisan boost:install` command in a Laravel project
- Verify file generation in `.github/` directory
- Test with actual Copilot CLI: `copilot --additional-mcp-config @.github/mcp-config.json`
- Confirm "Configured MCP servers: laravel-boost" appears

#### Test Requirements
- All tests must pass before merging: `composer test`
- Code must pass linting: `composer test:lint`
- Maintain test coverage above 90%
- Write tests for all new features and bug fixes

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

1. **Platform Support**: Do NOT add Windows native support (only WSL on Windows)
2. **PHP Version**: Minimum PHP 8.3
3. **Laravel Version**: Minimum Laravel 12.x
4. **File Structure**: Keep configuration files in `.github/` directory
5. **MCP Strategy**: Always use FILE strategy

## Common Tasks

### Adding New Detection Methods
1. Update `systemDetectionConfig()` or `projectDetectionConfig()`
2. Keep detection lightweight and fast
3. Write tests in `tests/Feature/CopilotCliTest.php`
4. Run `composer test` to verify

### Modifying MCP Configuration
1. Edit `installFileMcp()` method
2. Ensure backward compatibility with existing configs
3. Validate JSON format before writing
4. Add test cases for the new configuration
5. Test with temporary directories in tests

### Adding New Features
1. Write tests first (TDD approach)
2. Implement the feature
3. Run `composer test` to verify tests pass
4. Run `composer lint` to format code
5. Verify with `composer test:lint`
6. Update documentation if needed

### Updating Documentation
- Update README.md for user-facing changes
- Keep installation instructions clear and concise
- Include version requirements
- Update this file for development guideline changes

## Dependencies Management

### Production Dependencies
- Keep minimal: only Laravel core packages and Laravel Boost
- PHP 8.3+ required
- Laravel 12.x+ required

### Development Dependencies
- `pestphp/pest` - Testing framework
- `orchestra/testbench` - Package testing support
- `mockery/mockery` - Mocking library
- `laravel/pint` - Code formatter

### Installation
- This package is development-only (require-dev in user projects)
- Run `composer install` to set up development environment

## Development Workflow

### Before Committing
1. Run all tests: `composer test`
2. Format code: `composer lint`
3. Verify formatting: `composer test:lint`
4. Check test coverage: `vendor/bin/pest --coverage`
5. Ensure all tests pass in CI (GitHub Actions)

### Continuous Integration
- **GitHub Actions**: `.github/workflows/tests.yml`
- Tests run on PHP 8.3 and 8.4
- Runs on every push and pull request to main branch
- Must pass before merging

### Composer Scripts
```bash
composer test          # Run all tests
composer lint          # Format code with Pint
composer test:lint     # Check code formatting
```

## Release Notes

When preparing releases, ensure:
- All tests pass: `composer test`
- Code is properly formatted: `composer test:lint`
- Test coverage remains above 90%
- Compatibility with Laravel Boost version requirements
- Test with latest Copilot CLI version
- Update README.md if installation steps change
- Update CHANGELOG.md (if exists)
- Follow semantic versioning
- Tag releases appropriately
