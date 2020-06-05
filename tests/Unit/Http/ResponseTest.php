<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Http;

use JsonException;
use SlimAPI\Http\Response;
use SlimAPI\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testGetJson(): void
    {
        $response = (new Response());
        self::assertNull($response->getJson());

        $response->getBody()->write('{"foo":"bar"}');
        self::assertSame(['foo' => 'bar'], $response->getJson()); // @phpstan-ignore-line
        self::assertEquals((object) ['foo' => 'bar'], $response->getJson(false));
    }

    public function testGetJsonError(): void
    {
        $response = (new Response());
        $response->getBody()->write('{');

        self::expectException(JsonException::class);
        self::expectExceptionMessage('Syntax error');
        $response->getJson();
    }

    public function testWithJson(): void
    {
        $response = (new Response())->withJson(['foo' => 'bar']);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('Content-type'));
        self::assertSame('{"foo":"bar"}', (string) $response->getBody());
    }

    public function testWithJsonError(): void
    {
        self::expectException(JsonException::class);
        self::expectExceptionMessage('Malformed UTF-8 characters, possibly incorrectly encoded');
        (new Response())->withJson(["bad utf\xFF"]);
    }

    public function testWithNoContent(): void
    {
        $response = new Response();
        $cloned = clone $response;

        self::assertEquals(
            $cloned->withStatus(204),
            $response->withNoContent(),
        );
    }
}
