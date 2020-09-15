<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Exception\Http;

use SlimAPI\Exception\Http\BadRequestException;
use SlimAPI\Tests\TestCase;

class BadRequestExceptionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $msg = "I'm bad request.";
        $e = new BadRequestException($msg);

        self::assertSame($msg, $e->getMessage());
        self::assertSame(400, $e->getCode());
        self::assertSame('BAD_REQUEST', $e->getError());
    }
}
