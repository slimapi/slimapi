<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\_fixtures;

use SlimAPI\App;
use SlimAPI\Configurator\ConfiguratorInterface;

class TestConfiguratorSecond implements ConfiguratorInterface
{
    public function configureApplication(App $application): void
    {
        // just for testing of referenced configurator in _fixtures/config.neon
    }
}
