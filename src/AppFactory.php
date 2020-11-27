<?php

declare(strict_types=1);

namespace SlimAPI;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Slim\CallableResolver;
use SlimAPI\Routing\RouteCollector;

class AppFactory
{
    private ResponseFactory $responseFactory;

    private Container $container;

    private CallableResolver $callableResolver;

    public function __construct(ResponseFactory $responseFactory, Container $container, CallableResolver $callableResolver)
    {
        $this->responseFactory = $responseFactory;
        $this->container = $container;
        $this->callableResolver = $callableResolver;
    }

    public function createApplication(): App
    {
        $routeCollector = new RouteCollector($this->responseFactory, $this->callableResolver, $this->container);

        return new App(
            $this->responseFactory,
            $this->container,
            $this->callableResolver,
            $routeCollector,
        );
    }
}
