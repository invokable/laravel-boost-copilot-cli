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

## Supported Platforms

- macOS
- WSL
  - Native Windows is not supported, please use [laravel-boost-phpstorm-copilot](https://github.com/invokable/laravel-boost-phpstorm-copilot)
  - If you manually create an MCP config file, you can use Laravel Boost without this package.
- Linux

### Laravel Sail

It also supports Laravel Sail. Before use, start it with `vendor/bin/sail up -d`. The `copilot` command runs outside of Sail.

### Testbench for Package Developers

> [!NOTE]
> When using Testbench for package development, the environment differs from a regular Laravel project. Some MCP tools that depend on application-specific features (like database connections, specific models, or application routes) may not be available or may not work as expected in the Testbench environment.

<details>
<summary>When developing Laravel packages, you can use Laravel Boost with Testbench.</summary>

#### Setup

First, ensure your `testbench.yaml` includes the following configuration:

```yaml
env:
  CACHE_STORE: array
```

This is **important** because Laravel Boost tries to use a database cache store by default, which will not work properly.

If `APP_ENV` is also set, set it to `APP_ENV: local`.

#### Installation

Run the boost installation command using Testbench:

```shell
vendor/bin/testbench boost:install
```

This will generate `.github/mcp-config.json` configured for Testbench environment with the following settings:

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

#### Usage

Use Copilot CLI with the generated config:

```shell
copilot --additional-mcp-config @.github/mcp-config.json
```

</details>

## Installation

```shell
composer require revolution/laravel-boost-copilot-cli --dev
```

## Usage

When you run the Laravel Boost installation command within your Laravel project, you'll see a `GitHub Copilot CLI` item added to the list. 

- Select `GitHub Copilot CLI` for the editor. This will create `.github/mcp-config.json`.
- Select `GitHub Copilot(Custom instructions)` for the guidelines. This will create `.github/instructions/laravel-boost.instructions.md`.

If you want to create the regular `.github/copilot-instructions.md`, select `GitHub Copilot` for the guidelines.

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

## TODO
- Migrate to Agent Skills when Laravel Boost supports it.

## License

MIT
