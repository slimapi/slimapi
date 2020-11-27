<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Validation\Middleware;

use SlimAPI\App;
use SlimAPI\Exception\Validation\ResponseException;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;
use SlimAPI\Tests\Functional\TestCase;

class ResponseMiddlewareTest extends TestCase
{
    /** @var App */
    protected static App $application;

    public function testValidationSuccess(): void
    {
        $response = self::$application->handle(self::createRequestGet('/bar/v1/foo', ['date' => 'now']));
        $data = $response->getJson(true);

        self::assertSame('GET', $data['method']);
        self::assertSame('/bar/v1/foo', $data['pattern']);
        self::assertSame('now', $data['query']['date']);
    }

    public function testValidationFail(): void
    {
        self::expectException(ResponseException::class);
        self::expectExceptionMessage(sprintf(
            '[' .
                '{"property":"","message":"%s","constraint":"additionalProp"},' .
                '{"property":"","message":"%s","constraint":"additionalProp"}' .
            ']',
            'The property pattern is not defined and the definition does not allow additional properties',
            'The property query is not defined and the definition does not allow additional properties',
        ));
        self::expectExceptionCode(500);
        self::$application->handle(self::createRequestGet('/bar/v1/fail'));
    }

    public function testSkipNonSuccessful(): void
    {
        $response = self::$application->handle(self::createRequestGet('/bar/v1/error'));
        self::assertSame(422, $response->getStatusCode());
    }

    public function testManualSkip(): void
    {
        $response = self::$application->handle(self::createRequestGet('/bar/v1/skip'));
        $data = $response->getJson(true);

        self::assertSame('GET', $data['method']);
        self::assertSame('/bar/v1/skip', $data['pattern']);
    }

    public function testWhenOnlyRequestSchemaIsAvailable(): void
    {
        $response = self::$application->handle(self::createRequestPut('/bar/v1/foo', ['id' => 123]));
        $data = $response->getJson(true);

        self::assertSame('PUT', $data['method']);
        self::assertSame('/bar/v1/foo', $data['pattern']);
    }

    public function actionHandlerBar(Request $request, Response $response): Response
    {
        $pattern = $request->getRoute()->getPattern();
        $response = $response->withJson([
            'method' => $request->getMethod(),
            'pattern' => $pattern,
            'query' => $request->getQueryParams(),
        ]);

        if ($pattern === '/bar/v1/error') {
            $response = $response->withStatus(422);
        }

        if ($pattern === '/bar/v1/skip') {
            $response = $response->skipValidation();
        }

        return $response;
    }

    public static function setUpBeforeClass(): void
    {
        self::cleanup();

        $container = self::createContainer(__DIR__ . '/../fixtures/validation.neon');
        self::$application = $container->getByType(App::class);
    }
}
