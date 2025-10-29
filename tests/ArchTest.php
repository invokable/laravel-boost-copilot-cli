<?php

declare(strict_types=1);

arch('strict types')
    ->expect('Revolution\Laravel\Boost')
    ->toUseStrictTypes();

arch('no debugging functions')
    ->expect(['dd', 'dump', 'var_dump', 'print_r', 'die'])
    ->not->toBeUsed();

arch('avoid open for extension')
    ->expect('Revolution\Laravel\Boost')
    ->classes()
    ->not->toBeFinal();
