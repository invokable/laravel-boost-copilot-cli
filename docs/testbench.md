# Testbench for Package Developers

When developing Laravel packages, you can use Laravel Boost with Testbench.

> [!NOTE]
> When using Testbench for package development, the environment differs from a regular Laravel project. Some MCP tools that depend on application-specific features (like database connections, specific models, or application routes) may not be available or may not work as expected in the Testbench environment.

## Setup

First, ensure your `testbench.yaml` includes the following configuration:

```yaml
env:
  CACHE_STORE: array
```

This is **important** because Laravel Boost tries to use a database cache store by default, which will not work properly.

If `APP_ENV` is also set, set it to `APP_ENV: local`.

## Installation

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

## Usage

Use Copilot CLI with the generated config:

```shell
copilot --additional-mcp-config @.github/mcp-config.json
```
