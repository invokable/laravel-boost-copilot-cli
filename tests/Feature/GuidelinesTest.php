<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;

test('guidelines file exists', function (): void {
    $guidelinePath = __DIR__.'/../../resources/boost/guidelines/core.blade.php';

    expect(File::exists($guidelinePath))->toBeTrue();
});

test('guidelines contain GitHub Copilot CLI section', function (): void {
    $guidelinePath = __DIR__.'/../../resources/boost/guidelines/core.blade.php';
    $content = File::get($guidelinePath);

    expect($content)->toContain('## Laravel Boost for GitHub Copilot CLI')
        ->and($content)->toContain('MCP Configuration File Required');
});

test('guidelines contain MCP config restart instruction', function (): void {
    $guidelinePath = __DIR__.'/../../resources/boost/guidelines/core.blade.php';
    $content = File::get($guidelinePath);

    expect($content)->toContain('copilot --additional-mcp-config @.github/mcp-config.json --continue')
        ->and($content)->toContain('--additional-mcp-config');
});

test('guidelines contain testbench section', function (): void {
    $guidelinePath = __DIR__.'/../../resources/boost/guidelines/core.blade.php';
    $content = File::get($guidelinePath);

    expect($content)->toContain('@if(defined(\'TESTBENCH_CORE\'))')
        ->and($content)->toContain('Laravel Package Development Environment')
        ->and($content)->toContain('vendor/bin/testbench');
});

test('guidelines render correctly without testbench', function (): void {
    $guidelinePath = __DIR__.'/../../resources/boost/guidelines/core.blade.php';
    $template = File::get($guidelinePath);

    $rendered = Blade::render($template);

    expect($rendered)->toContain('## Laravel Boost for GitHub Copilot CLI')
        ->and($rendered)->toContain('MCP Configuration File Required')
        ->and($rendered)->toContain('copilot --additional-mcp-config @.github/mcp-config.json --continue')
        ->and($rendered)->not->toContain('Laravel Package Development Environment')
        ->and($rendered)->not->toContain('@if');
});

test('guidelines render correctly with testbench', function (): void {
    if (! defined('TESTBENCH_CORE')) {
        define('TESTBENCH_CORE', true);
    }

    $guidelinePath = __DIR__.'/../../resources/boost/guidelines/core.blade.php';
    $template = File::get($guidelinePath);

    $rendered = Blade::render($template);

    expect($rendered)->toContain('## Laravel Boost for GitHub Copilot CLI')
        ->and($rendered)->toContain('MCP Configuration File Required')
        ->and($rendered)->toContain('Laravel Package Development Environment')
        ->and($rendered)->toContain('vendor/bin/testbench')
        ->and($rendered)->toContain('Not all Laravel Boost MCP tools will work correctly')
        ->and($rendered)->not->toContain('@if')
        ->and($rendered)->not->toContain('@endif');
})->skip('TESTBENCH_CORE constant cannot be conditionally defined in same process');
