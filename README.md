# Laravel Boost Custom Agent for GitHub Copilot CLI

[![tests](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml/badge.svg)](https://github.com/invokable/laravel-boost-copilot-cli/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/maintainability.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)
[![Code Coverage](https://qlty.sh/badges/d6389009-a7b8-45fe-a7b3-f07e4ff25a25/coverage.svg)](https://qlty.sh/gh/invokable/projects/laravel-boost-copilot-cli)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/invokable/laravel-boost-copilot-cli)

## Requirements

- PHP >= 8.2
- Laravel >= 12.x
- [Laravel Boost](https://github.com/laravel/boost) >= 2.0
- [Copilot CLI](https://github.com/github/copilot-cli) >= 0.0.343

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
  - `Boost MCP Server Configuration`: `.github/mcp-config.json`
- Next, you will see `Which AI agents would you like to configure?`. Select `GitHub Copilot CLI` for the AI agent.

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

### Autoloading `mcp-config.json`

`.bashrc` or `.zshrc` can be modified to automatically load the `mcp-config.json` file if it exists in the current project.

```shell
copilot_mcp() {
  local args=()

  if [ -f ".github/mcp-config.json" ]; then
    args+=(--additional-mcp-config @.github/mcp-config.json)
  fi

  if [ -f ".github/mcp-config.local.json" ]; then
    args+=(--additional-mcp-config @.github/mcp-config.local.json)
  fi

  copilot "${args[@]}" "$@"
}

alias copilot=copilot_mcp
```

```shell
copilot
copilot --resume
copilot --continue
```

### Local MCP Configuration

For MCP servers that require sensitive credentials (like Authorization headers), create `.github/mcp-config.local.json` for local-only settings. Add it to `.gitignore` to keep credentials out of version control.

```shell
echo ".github/mcp-config.local.json" >> .gitignore
```

Example `.github/mcp-config.local.json`:

```json
{
  "mcpServers": {
    "remote-mcp": {
      "type": "http",
      "url": "https://example.com/mcp",
      "headers": {
        "Authorization": "Bearer YOUR_TOKEN"
      },
      "tools": ["*"]
    }
  }
}
```

## Known issues

Copilot CLI does not load instructions after the `<`. This means that Laravel Boost's `<laravel-boost-guidelines>` is not loaded at all. Instruct `.github/copilot-instructions.md` to load `laravel-boost.instructions.md`.

## License

MIT
