<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Exception\Http;

use SlimAPI\Exception\Http\NotFoundException;
use SlimAPI\Tests\TestCase;

class NotFoundExceptionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $msg = 'Entity not found.';
        $e = new NotFoundException($msg);

        self::assertSame($msg, $e->getMessage());
        self::assertSame(404, $e->getCode());
        self::assertSame('NOT_FOUND', $e->getError());
    }
}
