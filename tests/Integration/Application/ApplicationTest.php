<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Integration\Application;

use SlimAPI\Application\Application;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;
use SlimAPI\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testRunWithoutRequest(): void
    {
        $container = self::createContainer(__FIXTURES_DIR__ . '/config.neon');
        $application = $container->getByType(Application::class);

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
