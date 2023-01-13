<?php

declare(strict_types=1);

namespace SlimAPI\Routing;

use Slim\Interfaces\RouteInterface;

class RouteCollector extends \Slim\Routing\RouteCollector
{
    /**
     * Create SlimAPI's Route object.
     */
    protected function createRoute(array $methods, string $pattern, mixed $callable): RouteInterface
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
