<?php

declare(strict_types=1);

namespace SlimAPI\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected const CACHE_DIR = __DIR__ . '/../tmp/cache';

    public static function cleanup(): void
    {
        shell_exec(sprintf('rm -rf %s', self::CACHE_DIR));
        mkdir(self::CACHE_DIR);
    }
}
