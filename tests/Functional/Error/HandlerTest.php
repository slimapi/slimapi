<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Error;

use SlimAPI\App;
use SlimAPI\Http\Response;
use SlimAPI\Tests\Functional\TestCase;

class HandlerTest extends TestCase
{
    protected static App $application;

    public function testDetermineStatusCode(): void
    {
        $response = $this->doRequest('/error/slimapi-http');
        self::assertSame(422, $response->getStatusCode());
    }

    public static function setUpBeforeClass(): void
    {
        self::cleanup();

        $container = self::createContainer(__DIR__ . '/fixtures/error.neon');
        self::$application = $container->getByType(App::class);
    }

    private function doRequest(string $path): Response
    {
        $request = $this->createRequestGet($path);
        return self::$application->handle($request);
    }
}
