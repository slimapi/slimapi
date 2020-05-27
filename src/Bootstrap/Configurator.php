<?php

declare(strict_types=1);

namespace SlimAPI\Bootstrap;

use Nette\Configurator as BaseConfigurator;
use Nette\DI\Config\Loader;
use Nette\DI\Extensions\DecoratorExtension;
use Nette\DI\Extensions\ExtensionsExtension;
use Nette\DI\Extensions\PhpExtension;
use SlimAPI\DI\Extension as SlimExtension;
use SlimAPI\DI\NeonAdapter;

class Configurator extends BaseConfigurator
{
    /** @var array */
    public $defaultExtensions = [ // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
        'php' => PhpExtension::class,
        'extensions' => ExtensionsExtension::class,
        'decorator' => DecoratorExtension::class,
        'slim' => SlimExtension::class,
    ];

    protected function createLoader(): Loader
    {
        $loader = parent::createLoader();
        $loader->addAdapter('neon', NeonAdapter::class);
        return $loader;
    }
}
