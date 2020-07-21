<?php

declare(strict_types=1);

namespace SlimAPI\Routing;

use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteCollector as BaseRouteCollector;

class RouteCollector extends BaseRouteCollector
{
    /**
     * Create SlimAPI's Route object.
     * @param array $methods
     * @param string $pattern
     * @param mixed $callable
     * @return RouteInterface
     */
    protected function createRoute(array $methods, string $pattern, $callable): RouteInterface
    {
        return new Route(
            $methods,
            $pattern,
            $callable,
            $this->responseFactory,
            $this->callableResolver,
            $this->container,
            $this->defaultInvocationStrategy,
            $this->routeGroups,
            $this->routeCounter,
        );
    }
}
