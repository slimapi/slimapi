<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Error\fixtures;

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    public static array $records;

    public function log(mixed $level, mixed $message, array $context = []): void
    {
        if (!isset(self::$records[$level])) {
            self::$records[$level] = [];
        }

        self::$records[$level][] = [$message, $context];
    }
}
