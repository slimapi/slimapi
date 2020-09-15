<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Exception\Http;

use SlimAPI\Exception\Http\UnauthorizedException;
use SlimAPI\Exception\InvalidArgumentException;
use SlimAPI\Tests\TestCase;

class UnauthorizedExceptionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $msg = 'Invalid login or password.';
        $e = new UnauthorizedException($msg);

        self::assertSame($msg, $e->getMessage());
        self::assertSame(401, $e->getCode());
        self::assertSame('UNAUTHORIZED', $e->getError());
        self::assertSame([], $e->getHeaders());
    }

    public function testWithAuthenticate(): void
    {
        $msg = 'Authorization failed.';
        $prev = new InvalidArgumentException();
        $e = new UnauthorizedException($msg, 'Bearer realm="example"', $prev);

        self::assertSame($msg, $e->getMessage());
        self::assertSame(401, $e->getCode());
        self::assertSame('UNAUTHORIZED', $e->getError());
        self::assertSame(['WWW-Authenticate' => 'Bearer realm="example"'], $e->getHeaders());
        self::assertSame($prev, $e->getPrevious());
    }
}
