<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Routing;

use SlimAPI\Configurator\ChainConfigurator;
use SlimAPI\Routing\Configurator;
use SlimAPI\Tests\Functional\TestCase;

class ExtensionTest extends TestCase
{
    public function testLoadConfiguration(): void
    {
        $container = self::createContainer(__DIR__ . '/fixtures/routes_success.neon');
        self::assertInstanceOf(Configurator::class, $container->getService('routes.configurator'));

        $chainConfigurator = $container->getByType(ChainConfigurator::class);
        self::assertInstanceOf(Configurator::class, $chainConfigurator->getConfigurators()[0], 'First in stack');
    }
}
