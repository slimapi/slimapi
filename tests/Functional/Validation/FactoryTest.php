<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Validation;

use SlimAPI\App;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;
use SlimAPI\Tests\Functional\TestCase;

class FactoryTest extends TestCase
{
    public function testDisabledRequestValidation(): void
    {
        $application = $this->createApplication(__DIR__ . '/fixtures/validation_disabled_request.neon');
        $response = $application->handle($this->createRequestPost('/foo/v1/bar', ['id' => 'not-type-number'], []));
        $data = $response->getJson(true);

        self::assertSame('POST', $data['method']);
        self::assertSame('/foo/v1/bar', $data['pattern']);
        self::assertCount(0, $data['validation']);
    }

    public function testDisabledResponseValidation(): void
    {
        $application = $this->createApplication(__DIR__ . '/fixtures/validation_disabled_response.neon');
        $response = $application->handle($this->createRequestGet('/bar/v1/foo'));
        $data = $response->getJson(true);

        self::assertSame(['this-is-not' => 'defined-in-json-schema'], $data);
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

    public function actionHandlerBar(Request $request, Response $response): Response
    {
        return $response->withJson(['this-is-not' => 'defined-in-json-schema']);
    }

    private function createApplication(string $config): App
    {
        self::cleanup();

        $container = self::createContainer($config);
        return $container->getByType(App::class);
    }
}
