<?php

declare(strict_types=1);

namespace SlimAPI\Routing;

use SlimAPI\App;
use SlimAPI\Configurator\ConfiguratorInterface;
use SlimAPI\Exception\LogicException;

class Configurator implements ConfiguratorInterface
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function configureApplication(App $application): void
    {
        $container = $application->getContainer();
        foreach ($this->config as $name => [$method, $pattern, $handler]) {
            /** @var Route $route */
            $route = $application->map((array) $method, $pattern, function (...$args) use ($container, $handler) { // phpcs:ignore SlevomatCodingStandard.Functions.StaticClosure
                [$class, $action] = explode('::', $handler);
                $method = 'action' . ucfirst($action);

                $callable = [$container->getByType($class), $method];
                if (!is_callable($callable, false)) {
                    throw new LogicException(sprintf('Callback %s::%s is not callable.', $class, $method));
                }

                return $callable(...$args);
            });

            $route->setName((string) $name);
            $route->setAttribute(RouteContext::ROUTE_SETTINGS, Settings::from($this->config[$name][3] ?? []));
        }
    }
}
