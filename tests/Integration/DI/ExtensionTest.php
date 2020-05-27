<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Integration\DI;

use Slim\Psr7\Factory\ResponseFactory;
use SlimAPI\Application\Application;
use SlimAPI\Application\ChainConfigurator;
use SlimAPI\DI\ContainerAdapter;
use SlimAPI\Http\Request;
use SlimAPI\Tests\TestCase;

class ExtensionTest extends TestCase
{
    public function testServices(): void
    {
        $container = self::createContainer(__FIXTURES_DIR__ . '/config.neon');

        self::assertInstanceOf(ContainerAdapter::class, $container->getService('slim.container'));
        self::assertInstanceOf(ChainConfigurator::class, $container->getService('slim.chainConfigurator'));
        self::assertInstanceOf(Request::class, $container->getService('slim.request'));
        self::assertInstanceOf(ResponseFactory::class, $container->getService('slim.responseFactory'));
        self::assertInstanceOf(Application::class, $container->getService('slim.application'));
    }
}
