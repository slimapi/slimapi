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
    protected const CACHE_DIR = __DIR__ . '/../tmp/cache';

    public static function cleanup(): void
    {
        shell_exec(sprintf('rm -rf %s', self::CACHE_DIR));
        mkdir(self::CACHE_DIR);
    }

    protected static function createConfigurator(string $config, bool $debugMode = false): Configurator
    {
        $configurator = new Configurator();
        $configurator->addConfig($config);
        $configurator->setDebugMode($debugMode);
        $configurator->setTempDirectory(self::CACHE_DIR . '/../');

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
