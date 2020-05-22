<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Bootstrap;

use SlimAPI\Bootstrap\Configurator;
use SlimAPI\Tests\TestCase;

class ConfiguratorTest extends TestCase
{
    public function testCreateLoader(): void
    {
        $configurator = new Configurator();
        $configurator->setTempDirectory(self::TMP_DIR);
        $configurator->addConfig(__DIR__ . '/../DI/Config/Adapters/config.neon');
        $container = $configurator->createContainer();
        self::assertSame('foo', $container->getParameters()['specialParameter']);
    }
}
