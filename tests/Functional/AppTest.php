<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional;

use SlimAPI\App;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;

class AppTest extends TestCase
{
    public function testRunWithoutRequest(): void
    {
        $container = self::createContainer(__DIR__ . '/DI/fixtures/config.neon');
        $application = $container->getByType(App::class);

        $uri = '/application-test';
        $application->get($uri, function (Request $request, Response $response): Response { // phpcs:ignore SlevomatCodingStandard.Functions.StaticClosure
            return $response->write('OK - ' . $request->getMethod());
        });

        $_SERVER['REQUEST_URI'] = $uri;

        ob_start();
        $application->run();
        $response = ob_get_clean();
        self::assertSame('OK - GET', $response);
    }
}
