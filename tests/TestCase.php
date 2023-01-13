<?php

declare(strict_types=1);

namespace SlimAPI\Tests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected const CACHE_DIR = __DIR__ . '/../tmp/cache';

    public static function cleanup(): void
    {
        shell_exec(sprintf('rm -rf %s', self::CACHE_DIR));
        mkdir(self::CACHE_DIR);
    }
}
