# Laravel Boost Custom CodeEnvironment for GitHub Copilot CLI

[![tests](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml/badge.svg)](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/maintainability.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)
[![Code Coverage](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/coverage.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/invokable/laravel-boost-copilot-cli)

## Requirements
- PHP >= 8.3
- Laravel >= 12.x
- [Laravel Boost](https://github.com/laravel/boost) >= 1.7
- [Copilot CLI](https://github.com/github/copilot-cli) >= 0.0.343

## Supported OS
- macOS
- WSL (Native Windows is not supported, please use [laravel-boost-phpstorm-copilot](https://github.com/invokable/laravel-boost-phpstorm-copilot))
- Linux

> **Note**: It also supports Laravel Sail. Before use, start it with `vendor/bin/sail up -d`.

## Installation

```shell
composer require revolution/laravel-boost-copilot-cli --dev
```

## Usage

When you run the Laravel Boost installation command within your Laravel project, you'll see a `GitHub Copilot CLI` item added to the list. Select it to generate `.github/mcp-config.json` for Copilot CLI. To generate `.github/copilot-instructions.md`, also select the boost standard `GitHub Copilot`.

```shell
php artisan boost:install
```

When running the copilot command, specify `.github/mcp-config.json` using the `--additional-mcp-config` option.

```shell
copilot --additional-mcp-config @.github/mcp-config.json
```

If, after starting Copilot, `Configured MCP servers: laravel-boost` appears, the setup was successful.

From now on, always specify the `--additional-mcp-config` option when using the `copilot` command. It can be used together with other options.

```shell
copilot --additional-mcp-config @.github/mcp-config.json --resume
```
```shell
copilot --additional-mcp-config @.github/mcp-config.json --continue
```

## License
MIT
