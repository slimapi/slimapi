<?php

declare(strict_types=1);

namespace SlimAPI\Configurator;

use SlimAPI\App;

/**
 * @link https://github.com/o2ps/SlimNetteBridge/blob/1a594a/src/Application/ApplicationConfigurator.php
 */
interface ConfiguratorInterface
{
    public function configureApplication(App $application): void;
}
