<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Integration\_fixtures;

use SlimAPI\Application\Application;
use SlimAPI\Application\ApplicationConfigurator;

class TestConfiguratorSecond implements ApplicationConfigurator
{
    public function configureApplication(Application $application): void
    {
        // just for testing of referenced configurator in slim_extension.neon
    }
}
