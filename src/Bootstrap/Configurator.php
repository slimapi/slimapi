<?php

declare(strict_types=1);

namespace SlimAPI\Bootstrap;

use Nette\Bootstrap\Extensions\PhpExtension;
use Nette\DI\Config\Loader;
use Nette\DI\Extensions\DecoratorExtension;
use Nette\DI\Extensions\ExtensionsExtension;
use SlimAPI\DI\Extension as SlimApiExtension;
use SlimAPI\DI\NeonAdapter;
use SlimAPI\Routing\Extension as RoutesExtension;

class Configurator extends \Nette\Bootstrap\Configurator
{
    public array $defaultExtensions = [
        'php' => PhpExtension::class,
        'extensions' => ExtensionsExtension::class,
        'decorator' => DecoratorExtension::class,
        'slimapi' => SlimApiExtension::class,
        'routes' => RoutesExtension::class,
    ];

    protected function createLoader(): Loader
    {
        $loader = parent::createLoader();
        $loader->addAdapter('neon', NeonAdapter::class);
        return $loader;
    }
}
