<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Bootstrap;

use SlimAPI\Bootstrap\Configurator;
use SlimAPI\Tests\TestCase;

class ConfiguratorTest extends TestCase
{
    public function testCreateLoader(): void
    {
        $configurator = new Configurator();
        $configurator->setTempDirectory(__TMP_DIR__);
        $configurator->addConfig(__FIXTURES_DIR__ . '/neon_env_substitution.neon');
        $container = $configurator->createContainer();
        self::assertSame('test_env_val', $container->getParameters()['specialParameter'], 'NEON env substitution failed');
    }
}
