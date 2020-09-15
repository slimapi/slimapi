<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Exception\Http;

use SlimAPI\Exception\Http\Exception;
use SlimAPI\Exception\InvalidArgumentException;
use SlimAPI\Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $msg = 'msg';
        $code = 999;
        $error = 'special-error';
        $headers = ['x-foo' => 'bar'];
        $prev = new InvalidArgumentException();
        $e = new Exception($msg, $code, $error, $headers, $prev);

        self::assertSame($msg, $e->getMessage());
        self::assertSame($code, $e->getCode());
        self::assertSame($error, $e->getError());
        self::assertSame($headers, $e->getHeaders());
        self::assertSame($prev, $e->getPrevious());
    }

    public function testGenerateErrorFromCode(): void
    {
        $msg = 'msg';
        $code = 451;
        $e = new Exception($msg, $code);

        self::assertSame($msg, $e->getMessage());
        self::assertSame($code, $e->getCode());
        self::assertSame('UNAVAILABLE_FOR_LEGAL_REASONS', $e->getError());
    }

    public function testGenerateErrorFail(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Argument $error has to be set.');
        new Exception('msg', 999);
    }
}
