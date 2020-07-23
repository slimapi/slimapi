<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Unit\Testing;

use SlimAPI\Testing\RequestHelper;
use SlimAPI\Tests\TestCase;

class RequestHelperTest extends TestCase
{
    use RequestHelper;

    public function testCreateRequestGet(): void
    {
        $request = self::createRequestGet('/req-helper-test/get');
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/req-helper-test/get', $request->getUri()->getPath());
    }

    public function testCreateRequestGetWithQuery(): void
    {
        $request = self::createRequestGet('/req-helper-test/get/query', ['foo' => 'bar']);
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/req-helper-test/get/query', $request->getUri()->getPath());
        self::assertSame(['foo' => 'bar'], $request->getQueryParams());
    }

    public function testCreateRequestGetWithQueryAndHeaders(): void
    {
        $request = self::createRequestGet('/req-helper-test/get/query-headers', ['foo' => 'foo'], ['X-Foo' => 'bar']);
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/req-helper-test/get/query-headers', $request->getUri()->getPath());
        self::assertSame(['foo' => 'foo'], $request->getQueryParams());
        self::assertSame('bar', $request->getHeaderLine('X-Foo'));
    }

    public function testCreateRequestPost(): void
    {
        $request = self::createRequestPost('/req-helper-test/post', ['data' => 'foo']);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/req-helper-test/post', $request->getUri()->getPath());
        self::assertSame('{"data":"foo"}', (string) $request->getBody());
    }

    public function testCreateRequestPostWithQuery(): void
    {
        $request = self::createRequestPost('/req-helper-test/post/query', ['foo' => 'bar'], ['bar' => 'bar']);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/req-helper-test/post/query', $request->getUri()->getPath());
        self::assertSame('{"foo":"bar"}', (string) $request->getBody());
        self::assertSame(['bar' => 'bar'], $request->getQueryParams());
    }

    public function testCreateRequestPostWithQueryAndHeaders(): void
    {
        $request = self::createRequestPost('/req-helper-test/post/query-headers', ['x' => 'y'], ['z' => 'z'], ['X-Bar' => 'a']);
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/req-helper-test/post/query-headers', $request->getUri()->getPath());
        self::assertSame('{"x":"y"}', (string) $request->getBody());
        self::assertSame(['z' => 'z'], $request->getQueryParams());
        self::assertSame('a', $request->getHeaderLine('X-Bar'));
    }

    public function testCreateRequestPut(): void
    {
        $request = self::createRequestPut('/req-helper-test/put', ['data' => 'foo']);
        self::assertSame('PUT', $request->getMethod());
        self::assertSame('/req-helper-test/put', $request->getUri()->getPath());
        self::assertSame('{"data":"foo"}', (string) $request->getBody());
    }

    public function testCreateRequestPutWithQuery(): void
    {
        $request = self::createRequestPut('/req-helper-test/put/query', ['foo' => 'bar'], ['bar' => 'bar']);
        self::assertSame('PUT', $request->getMethod());
        self::assertSame('/req-helper-test/put/query', $request->getUri()->getPath());
        self::assertSame('{"foo":"bar"}', (string) $request->getBody());
        self::assertSame(['bar' => 'bar'], $request->getQueryParams());
    }

    public function testCreateRequestPutWithQueryAndHeaders(): void
    {
        $request = self::createRequestPut('/req-helper-test/put/query-headers', ['x' => 'y'], ['z' => 'z'], ['X-Bar' => 'a']);
        self::assertSame('PUT', $request->getMethod());
        self::assertSame('/req-helper-test/put/query-headers', $request->getUri()->getPath());
        self::assertSame('{"x":"y"}', (string) $request->getBody());
        self::assertSame(['z' => 'z'], $request->getQueryParams());
        self::assertSame('a', $request->getHeaderLine('X-Bar'));
    }

    public function testCreateRequestDelete(): void
    {
        $request = self::createRequestDelete('/req-helper-test/delete');
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/req-helper-test/delete', $request->getUri()->getPath());
    }

    public function testCreateRequestDeleteWithQuery(): void
    {
        $request = self::createRequestDelete('/req-helper-test/delete/query', ['foo' => 'bar']);
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/req-helper-test/delete/query', $request->getUri()->getPath());
        self::assertSame(['foo' => 'bar'], $request->getQueryParams());
    }

    public function testCreateRequestDeleteWithQueryAndHeaders(): void
    {
        $request = self::createRequestDelete('/req-helper-test/delete/query-headers', ['foo' => 'bar'], ['X-Delete' => 'me']);
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('/req-helper-test/delete/query-headers', $request->getUri()->getPath());
        self::assertSame(['foo' => 'bar'], $request->getQueryParams());
        self::assertSame('me', $request->getHeaderLine('X-Delete'));
    }
}
