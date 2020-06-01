<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Integration\Routing;

use SlimAPI\Application\ChainConfigurator;
use SlimAPI\Routing\Configurator;
use SlimAPI\Tests\TestCase;

class ExtensionTest extends TestCase
{
    public function testLoadConfiguration(): void
    {
        $container = self::createContainer(__FIXTURES_DIR__ . '/routes_success.neon');
        self::assertInstanceOf(Configurator::class, $container->getService('routes.configurator'));

        $chainConfigurator = $container->getByType(ChainConfigurator::class);
        self::assertInstanceOf(Configurator::class, $chainConfigurator->getConfigurators()[0], 'First in stack');
    }
}
