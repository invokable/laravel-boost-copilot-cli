## Laravel Boost for GitHub Copilot CLI

### MCP Configuration File Required
- If you cannot see the `laravel-boost` MCP server or tools, the user has likely forgotten to specify the MCP configuration file when starting Copilot CLI.
- Instruct the user to restart Copilot CLI with the correct command:
  ```
  copilot --additional-mcp-config @.github/mcp-config.json --continue
  ```
- The `--additional-mcp-config` option is **required** for every Copilot CLI session to access Laravel Boost MCP tools.

@if(defined('TESTBENCH_CORE'))
### Laravel Package Development Environment
- This is a **Laravel package development project** using Orchestra Testbench, not a standard Laravel application.
- The environment differs significantly from a typical Laravel project - there is no full application context, database, or application-specific models.
- **Important:** Not all Laravel Boost MCP tools will work correctly in this environment:
  - Tools that depend on database connections, specific models, application routes, or other application-specific features may not be available or may fail.
  - Tools like `database-query`, `database-schema`, `list-routes` may return limited or no results.
  - Basic tools like `application-info`, `list-artisan-commands`, `search-docs` should work normally.
- Focus on package-specific development tasks: writing tests, implementing package features, and ensuring compatibility with Laravel.
- Use `vendor/bin/testbench` commands instead of `php artisan` when needed.
@endif

