<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\Bootstrap;

use SlimAPI\Tests\Functional\TestCase;

class ConfiguratorTest extends TestCase
{
    public function testCreateLoader(): void
    {
        $configurator = self::createConfigurator(__DIR__ . '/../DI/fixtures/neon_env_substitution.neon');
        $container = $configurator->createContainer();
        self::assertSame('test_env_val', $container->getParameters()['specialParameter'], 'NEON env substitution failed');
    }
}
