<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\DI;

use Slim\Psr7\Factory\ResponseFactory;
use SlimAPI\App;
use SlimAPI\Configurator\ChainConfigurator;
use SlimAPI\DI\ContainerAdapter;
use SlimAPI\Http\Request;
use SlimAPI\Tests\TestCase;

class ExtensionTest extends TestCase
{
    public function testLoadConfiguration(): void
    {
        $container = self::createContainer(__FIXTURES_DIR__ . '/config.neon');

        self::assertInstanceOf(ContainerAdapter::class, $container->getService('slimapi.container'));
        self::assertInstanceOf(ChainConfigurator::class, $container->getService('slimapi.chainConfigurator'));
        self::assertInstanceOf(Request::class, $container->getService('slimapi.request'));
        self::assertInstanceOf(ResponseFactory::class, $container->getService('slimapi.responseFactory'));
        self::assertInstanceOf(App::class, $container->getService('slimapi.application'));
    }
}
