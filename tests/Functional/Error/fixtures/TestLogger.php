<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Error\fixtures;

use Psr\Log\AbstractLogger;

class TestLogger extends AbstractLogger
{
    public static array $records;

    /**
     * @param mixed $level
     * @param mixed $message
     * @param mixed[] $context
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        if (!isset(self::$records[$level])) {
            self::$records[$level] = [];
        }

        self::$records[$level][] = [$message, $context];
    }
}
