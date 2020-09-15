<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Exception\Http;

use SlimAPI\Exception\Http\UnprocessableEntityException;
use SlimAPI\Tests\TestCase;

class UnprocessableExceptionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $msg = 'Custom error detected.';
        $e = new UnprocessableEntityException('CUSTOM_ERROR', $msg);

        self::assertSame($msg, $e->getMessage());
        self::assertSame(422, $e->getCode());
        self::assertSame('CUSTOM_ERROR', $e->getError());
    }

    public function testGenerateMessageFromError(): void
    {
        $e = new UnprocessableEntityException('CUSTOM_ERROR_DETECTED');

        self::assertSame('Custom error detected', $e->getMessage());
        self::assertSame(422, $e->getCode());
        self::assertSame('CUSTOM_ERROR_DETECTED', $e->getError());
    }
}
