<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Http;

use JsonException;
use SlimAPI\Exception\Http\BadRequestException;
use SlimAPI\Exception\LogicException;
use SlimAPI\Http\Request;
use SlimAPI\Http\RequestFactory;
use SlimAPI\Tests\TestCase;

class RequestTest extends TestCase
{
    public function testGetJson(): void
    {
        $request = $this->createRequest();
        $request->getBody()->write('{"foo":"bar"}');
        self::assertSame(['foo' => 'bar'], $request->getJson(true));
        self::assertEquals((object) ['foo' => 'bar'], $request->getJson());
    }

    public function testGetJsonError(): void
    {
        $request = $this->createRequest();
        $request->getBody()->write('{');

        self::expectException(JsonException::class);
        self::expectExceptionMessage('Syntax error');
        $request->getJson();
    }

    public function testGetJsonEmpty(): void
    {
        $request = $this->createRequest();

        self::expectException(BadRequestException::class);
        self::expectExceptionMessage('Empty body cannot be parsed.');
        $request->getJson();
    }

    public function testGetRouteWithoutRoute(): void
    {
        $request = $this->createRequest();

        self::expectException(LogicException::class);
        self::expectExceptionMessage('No matched route. Missing call $app->addRoutingMiddleware()?');
        $request->getRoute();
    }

    public function testGetContentType(): void
    {
        $request = $this->createRequest();
        $request = $request->withHeader('Content-Type', 'application/foo');

        self::assertSame('application/foo', $request->getContentType());
    }

    public function testGetContentTypeEmpty(): void
    {
        $request = $this->createRequest();
        self::assertNull($request->getContentType());
    }

    public function testGetMediaType(): void
    {
        $request = $this->createRequest();
        $request = $request->withHeader('Content-Type', 'application/foo;charset=utf8;foo=bar');

        self::assertSame('application/foo', $request->getMediaType());
    }

    public function testGetMediaTypeEmpty(): void
    {
        $request = $this->createRequest();
        self::assertNull($request->getMediaType());
    }

    public function testGetMediaTypeInvalid(): void
    {
        $request = $this->createRequest('GET', '/foo/bar', [true]);
        $request = $request->withHeader('Content-Type', 'application/json');

        $GLOBALS['preg_split_return'] = false;
        self::assertNull($request->getMediaType());
        unset($GLOBALS['preg_split_return']);
    }

    private function createRequest(string $method = 'GET', string $uri = '/foo/bar', array $serverParams = []): Request
    {
        /** @var Request $request */
        $request = (new RequestFactory())->createServerRequest($method, $uri, $serverParams);
        return $request;
    }
}
