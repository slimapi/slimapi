<?php

declare(strict_types=1);

namespace SlimAPI\Tests;

use Nette\DI\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use SlimAPI\Bootstrap\Configurator;
use SlimAPI\Http\Request;

abstract class TestCase extends BaseTestCase
{
    public static function cleanup(): void
    {
        shell_exec(sprintf('rm -rf %s', __TMP_DIR__ . '/cache'));
    }

    protected static function createConfigurator(string $config, bool $debugMode = false): Configurator
    {
        $configurator = new Configurator();
        $configurator->addConfig($config);
        $configurator->setDebugMode($debugMode);
        $configurator->setTempDirectory(__TMP_DIR__);

        return $configurator;
    }

    protected static function createContainer(string $config, bool $debugMode = false): Container
    {
        return self::createConfigurator($config, $debugMode)->createContainer();
    }

    protected function createRequest(string $method, string $uri): Request
    {
        return new Request(
            $method,
            (new UriFactory())->createUri($uri),
            new Headers(),
            [],
            [],
            (new StreamFactory())->createStream(),
        );
    }
}
