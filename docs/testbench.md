# Testbench for Package Developers

When developing Laravel packages, you can use Laravel Boost with Testbench.

> [!NOTE]
> When using Testbench for package development, the environment differs from a regular Laravel project. Some MCP tools that depend on application-specific features (like database connections, specific models, or application routes) may not be available or may not work as expected in the Testbench environment.

## Setup (Recommended)

First, run the workbench install command to set up the Testbench environment:

```shell
vendor/bin/testbench workbench:install
```

This creates the `workbench` directory and adds a `build` script to your `composer.json`. The build script creates the SQLite database file that Laravel Boost needs for its default database cache.

Next, add `@build` to the `post-autoload-dump` scripts in your `composer.json`:

```json
{
    "scripts": {
        "post-autoload-dump": [
            "@clear",
            "@prepare",
            "@build"
        ],
        "build": "@php vendor/bin/testbench workbench:build --ansi"
    }
}
```

Then run composer to execute the build:

```shell
composer install
```

If `APP_ENV` is set in `testbench.yaml`, ensure it is set to `APP_ENV: local`.

## Alternative Setup

If you don't want to create the workbench directory and additional files, you can use the array cache store instead. Add the following to your `testbench.yaml`:

```yaml
env:
  CACHE_STORE: array
```

This bypasses the database cache requirement. If `APP_ENV` is also set, set it to `APP_ENV: local`.

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
