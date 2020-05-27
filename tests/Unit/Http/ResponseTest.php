<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Http;

use JsonException;
use SlimAPI\Http\Response;
use SlimAPI\Tests\TestCase;

class ResponseTest extends TestCase
{
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
