# Laravel Boost Custom Agent for GitHub Copilot CLI

[![tests](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml/badge.svg)](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/maintainability.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)
[![Code Coverage](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/coverage.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/invokable/laravel-boost-copilot-cli)

Docs: [English](https://kawax.biz/en/packages/laravel-boost-copilot-cli) [Japanese](https://kawax.biz/jp/packages/laravel-boost-copilot-cli)

## Requirements

- PHP >= 8.2
- Laravel >= 12.x
- [Laravel Boost](https://github.com/laravel/boost) >= 2.0
- [Copilot CLI](https://github.com/github/copilot-cli) >= 1.0.25

### Suggest
- [laravel-boost-phpstorm-copilot](https://github.com/invokable/laravel-boost-phpstorm-copilot) Laravel Boost for PhpStorm with Copilot plugin
- [laravel-copilot-sdk](https://github.com/invokable/laravel-copilot-sdk) Copilot SDK for Laravel

## Supported Platforms

- macOS
- WSL
  - Native Windows can also be installed, but WSL is still recommended.
- Linux

### Laravel Sail

It also supports Laravel Sail. Before use, start it with `vendor/bin/sail up -d`. The `copilot` command runs outside of Sail.

### Testbench for Package Developers

[testbench.md](./docs/testbench.md)

## Installation

```shell
composer require revolution/laravel-boost-copilot-cli --dev
```

## Usage

When you run the Laravel Boost installation command within your Laravel project, you'll see a `GitHub Copilot CLI` item added to the list.

- First, you will see `Which Boost features would you like to configure?`. The files will be installed depending on the features you select.
  - `AI Guidelines`: `.github/instructions/laravel-boost.instructions.md`
  - `Agent Skills`: `.github/skills`
  - `Boost MCP Server Configuration`: `.mcp.json`
- Next, you will see `Which AI agents would you like to configure?`. Select `GitHub Copilot CLI` for the AI agent.

```shell
php artisan boost:install
```

Copilot CLI automatically loads the `.mcp.json` configuration file from the project root, so no additional options are needed.

```shell
copilot
```

## License

MIT
