## Laravel Boost for GitHub Copilot CLI

This project uses Laravel Boost to provide MCP (Model Context Protocol) tools for GitHub Copilot CLI.
The MCP server is configured in `.mcp.json` and automatically loaded by Copilot CLI.

@if(defined('TESTBENCH_CORE'))
### Laravel Package Development Environment
- This is a **Laravel package development project** using Orchestra Testbench, not a standard Laravel application.
- The environment differs significantly from a typical Laravel project - there is no full application context, database, or application-specific models.
- **Important:** Not all Laravel Boost MCP tools will work correctly in this environment:
  - Tools that depend on database connections, specific models, application routes, or other application-specific features may not be available or may fail.
  - Tools like `database-query`, `database-schema` may return limited or no results.
  - Basic tools like `application-info`, `search-docs` should work normally.
- Focus on package-specific development tasks: writing tests, implementing package features, and ensuring compatibility with Laravel.
- Use `vendor/bin/testbench` commands instead of `php artisan` when needed.
@endif
