<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Validation\Middleware;

use Slim\Psr7\Factory\StreamFactory;
use SlimAPI\App;
use SlimAPI\Exception\Http\BadRequestException;
use SlimAPI\Exception\LogicException;
use SlimAPI\Exception\Validation\RequestException;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;
use SlimAPI\Tests\Functional\TestCase;

class RequestMiddlewareTest extends TestCase
{
    /** @var App */
    protected static App $application;

    public function testValidationSuccess(): void
    {
        $response = self::$application->handle($this->createRequestPost('/foo/v1/bar', ['id' => 123], []));
        $data = $response->getJson(true);

        self::assertSame('POST', $data['method']);
        self::assertSame('/foo/v1/bar', $data['pattern']);
        self::assertCount(2, $data['validation']);
    }

    public function testValidationFailed(): void
    {
        self::expectException(RequestException::class);
        self::expectExceptionMessage(
            '[{"property":"id","message":"String value found, but a number is required","constraint":"type"}]',
        );
        self::expectExceptionCode(400);
        self::$application->handle($this->createRequestPost('/foo/v1/bar', ['id' => 'not-type-number'], []));
    }

    public function testSkipValidation(): void
    {
        $response = self::$application->handle($this->createRequestDelete('/foo/v1/bar'));
        $data = $response->getJson(true);

        self::assertSame('DELETE', $data['method']);
        self::assertSame('/foo/v1/bar', $data['pattern']);
        self::assertCount(0, $data['validation']);
    }

    public function testGetSchemaApiBlueprintOptional(): void
    {
        $response = self::$application->handle($this->createRequestPut('/foo/v1/bar/123', ['foo' => 'bar']));
        $data = $response->getJson(true);

        self::assertSame('/foo/v1/bar/[{id}]', $data['pattern']);
    }

    public function testGetSchemaMissing(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage('Validation schema for request [PUT /foo/v1/missing-schema] has not been found.');
        self::$application->handle($this->createRequestPut('/foo/v1/missing-schema', ['foo' => 'bar']));
    }

    public function testAutoSkipWhenSchemaForRequestMissing(): void
    {
        $response = self::$application->handle($this->createRequestGet('/foo/v1/bar', ['foo' => 'bar']));
        $data = $response->getJson(true);

        self::assertSame('GET', $data['method']);
        self::assertSame('/foo/v1/bar', $data['pattern']);
        self::assertCount(1, $data['validation']);
    }

    public function testBadRequestType(): void
    {
        $request = $this->createRequest('POST', '/foo/v1/bar');
        $body = (new StreamFactory())->createStream();
        $body->write('foo-bar');
        $request = $request->withBody($body);
        $request = $request->withHeader('Content-Type', 'text/plain');

        self::expectException(BadRequestException::class);
        self::expectExceptionMessage("Supported content-type is 'application/json' only.");
        self::expectExceptionCode(400);
        self::$application->handle($request);
    }

    public function testMissingRequestBody(): void
    {
        $request = $this->createRequest('POST', '/foo/v1/bar');

        self::expectException(BadRequestException::class);
        self::expectExceptionMessage('Missing request body.');
        self::expectExceptionCode(400);
        self::$application->handle($request);
    }

    public function testBadRequestBody(): void
    {
        $request = $this->createRequest('POST', '/foo/v1/bar');
        $body = (new StreamFactory())->createStream();
        $body->write("\x00");
        $request = $request->withBody($body);
        $request = $request->withHeader('Content-Type', 'application/json');

        self::expectException(BadRequestException::class);
        self::expectExceptionMessage('Bad request body.');
        self::expectExceptionCode(400);
        self::$application->handle($request);
    }

    public function actionHandlerFoo(Request $request, Response $response): Response
    {
        $pattern = $request->getRoute()->getPattern();
        $response = $response->withJson([
            'method' => $request->getMethod(),
            'pattern' => $pattern,
            'validation' => $request->getValidationSchema(),
        ]);

        return $response;
    }

    public static function setUpBeforeClass(): void
    {
        self::cleanup();

        $container = self::createContainer(__DIR__ . '/../fixtures/validation.neon');
        self::$application = $container->getByType(App::class);
    }
}
