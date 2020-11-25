<?php

declare(strict_types=1);

namespace SlimAPI;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\CallableResolver;
use SlimAPI\Routing\RouteCollector;

class AppFactory
{
    private ResponseFactoryInterface $responseFactory;

    private ContainerInterface $container;

    public function __construct(ResponseFactoryInterface $responseFactory, ContainerInterface $container)
    {
        $this->responseFactory = $responseFactory;
        $this->container = $container;
    }

    public function createApplication(): App
    {
        $callableResolver = new CallableResolver($this->container);
        $routeCollector = new RouteCollector($this->responseFactory, $callableResolver, $this->container);

        return new App(
            $this->responseFactory,
            $this->container,
            $callableResolver,
            $routeCollector,
        );
    }
}
