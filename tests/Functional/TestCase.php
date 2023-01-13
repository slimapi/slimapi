<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional;

use Nette\DI\Container;
use SlimAPI\Bootstrap\Configurator;
use SlimAPI\Testing\RequestHelper;

class TestCase extends \SlimAPI\Tests\TestCase
{
    use RequestHelper;

    protected static function createConfigurator(string $config, bool $debugMode = false): Configurator
    {
        $configurator = new Configurator();
        $configurator->addConfig($config);
        $configurator->setDebugMode($debugMode);
        $configurator->setTempDirectory(self::CACHE_DIR . '/..');

        return $configurator;
    }

    protected static function createContainer(string $config, bool $debugMode = false): Container
    {
        return self::createConfigurator($config, $debugMode)->createContainer();
    }
}
