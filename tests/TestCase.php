<?php

declare(strict_types=1);

namespace SlimAPI\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected const TMP_DIR = __DIR__ . '/../tmp';

    public static function cleanup(): void
    {
        shell_exec(sprintf('rm -rf %s', self::TMP_DIR . '/cache'));
    }
}
