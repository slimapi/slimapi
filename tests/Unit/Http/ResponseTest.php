<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Http;

use JsonException;
use SlimAPI\Exception\Http\InternalServerError;
use SlimAPI\Http\Response;
use SlimAPI\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testGetJson(): void
    {
        $response = (new Response());
        $response->getBody()->write('{"foo":"bar"}');
        self::assertSame(['foo' => 'bar'], $response->getJson(true));
        self::assertEquals((object) ['foo' => 'bar'], $response->getJson());
    }

    public function testGetJsonError(): void
    {
        $response = (new Response());
        $response->getBody()->write('{');

        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Syntax error');
        $response->getJson();
    }

    public function testGetJsonEmpty(): void
    {
        $response = (new Response());

        $this->expectException(InternalServerError::class);
        $this->expectExceptionMessage('Empty body cannot be parsed.');
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
        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Malformed UTF-8 characters, possibly incorrectly encoded');
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

    public function testIsOk(): void
    {
        $response = (new Response())->withStatus(200);
        self::assertTrue($response->isOk());
    }

    public function testIsSuccessful(): void
    {
        $response = (new Response())->withStatus(201);
        self::assertTrue($response->isSuccessful());
    }
}
