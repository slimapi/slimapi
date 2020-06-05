<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Http;

use JsonException;
use SlimAPI\Tests\TestCase;

class RequestTest extends TestCase
{
    public function testGetJson(): void
    {
        $request = $this->createRequest('GET', '/foo/bar');
        self::assertNull($request->getJson());

        $request->getBody()->write('{"foo":"bar"}');
        self::assertSame(['foo' => 'bar'], $request->getJson()); // @phpstan-ignore-line
        self::assertEquals((object) ['foo' => 'bar'], $request->getJson(false));
    }

    public function testGetJsonError(): void
    {
        $request = $this->createRequest('GET', '/foo/bar');
        $request->getBody()->write('{');

        self::expectException(JsonException::class);
        self::expectExceptionMessage('Syntax error');
        $request->getJson();
    }
}
