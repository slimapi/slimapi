<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Exception\Http;

use SlimAPI\Exception\Http\NotImplementedException;
use SlimAPI\Tests\TestCase;

class NotImplementedExceptionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $msg = 'This method is not implemented.';
        $e = new NotImplementedException($msg);

        self::assertSame($msg, $e->getMessage());
        self::assertSame(501, $e->getCode());
        self::assertSame('NOT_IMPLEMENTED', $e->getError());
    }
}
