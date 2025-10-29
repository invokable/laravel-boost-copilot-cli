# Laravel Boost Custom CodeEnvironment for GitHub Copilot CLI

[![tests](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml/badge.svg)](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/maintainability.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)
[![Code Coverage](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/coverage.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/invokable/laravel-boost-copilot-cli)

## Requirements
- PHP >= 8.3
- Laravel >= 12.x
- [Laravel Boost](https://github.com/laravel/boost) >= 1.6
- [Copilot CLI](https://github.com/github/copilot-cli) >= 0.0.343

## Support OS
- macOS
- WSL/Ubuntu (Standard Windows environment is not supported)
- Linux

## Installation

```shell
composer require revolution/laravel-boost-copilot-cli --dev
```

## Usage

When you run the standard Laravel Boost installation command within your Laravel project, you'll see a `GitHub Copilot CLI` item added to the list. Select it to generate `.github/copilot-instructions.md` and `.github/mcp-config.json` for Copilot CLI.

```shell
php artisan boost:install
```

When running the copilot command, specify `.github/mcp-config.json` using the `--additional-mcp-config` option.

```shell
copilot --additional-mcp-config @.github/mcp-config.json
```

If, after starting Copilot, `Configured MCP servers: laravel-boost` appears, the setup was successful.

## Development

### Running Tests

```shell
composer test
```

### Running Linter

```shell
composer lint
```

### Testing Linter Without Making Changes

```shell
composer test:lint
```

## LICENCE
MIT
