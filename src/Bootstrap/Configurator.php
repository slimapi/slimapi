<?php

declare(strict_types=1);

namespace SlimAPI\Bootstrap;

use Nette\Configurator as BaseConfigurator;
use Nette\DI\Config\Loader;
use SlimAPI\DI\Config\Adapters\NeonAdapter;

class Configurator extends BaseConfigurator
{
    protected function createLoader(): Loader
    {
        $loader = parent::createLoader();
        $loader->addAdapter('neon', NeonAdapter::class);
        return $loader;
    }
}
