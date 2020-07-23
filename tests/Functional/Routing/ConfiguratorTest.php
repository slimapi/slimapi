<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Routing;

use Slim\Routing\Route;
use SlimAPI\App;
use SlimAPI\Exception\LogicException;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;
use SlimAPI\Tests\Functional\TestCase;

class ConfiguratorTest extends TestCase
{
    public function testConfigureApplicationSuccess(): void
    {
        $container = self::createContainer(__DIR__ . '/fixtures/routes_success.neon');
        $application = $container->getByType(App::class);

        $routes = $application->getRouteCollector()->getRoutes();
        if ($routes === []) {
            self::fail('Routes definition failed.');
        }

        /** @var Route $first */
        $first = $routes['route0'];
        self::assertSame(['GET'], $first->getMethods());
        self::assertSame('/routes-test', $first->getPattern());
        self::assertSame('0', $first->getName());

        /** @var Route $second */
        $second = $routes['route1'];
        self::assertSame(['POST'], $second->getMethods());
        self::assertSame('/routes-test-2', $second->getPattern());
        self::assertSame('routeName', $second->getName());

        $response = $application->handle(self::createRequestGet('/routes-test'));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"response-test-success-method":"GET"}', (string) $response->getBody());

        $response = $application->handle(self::createRequestPost('/routes-test-2', []));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"response-test-success-method":"POST"}', (string) $response->getBody());
    }

    public function testConfigureApplicationFail(): void
    {
        $container = self::createContainer(__DIR__ . '/fixtures/routes_fail.neon');
        $application = $container->getByType(App::class);

        self::expectException(LogicException::class);
        self::expectExceptionMessage('Callback SlimAPI\Tests\Functional\Routing\ConfiguratorTest::actionFail is not callable.');
        $application->handle(self::createRequestGet('/routes-test-fail'));
    }

    public function actionTestSuccess(Request $request, Response $response): Response
    {
        return $response->withJson(['response-test-success-method' => $request->getMethod()]);
    }
}
