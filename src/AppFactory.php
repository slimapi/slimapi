<?php

declare(strict_types=1);

namespace SlimAPI;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseFactoryInterface as ResponseFactory;
use Slim\CallableResolver;
use SlimAPI\Configurator\ConfiguratorInterface as Configurator;
use SlimAPI\Routing\RouteCollector;

class AppFactory
{
    private ResponseFactory $responseFactory;

    private Container $container;

    private Configurator $configurator;

    public function __construct(ResponseFactory $responseFactory, Container $container, Configurator $configurator)
    {
        $this->responseFactory = $responseFactory;
        $this->container = $container;
        $this->configurator = $configurator;
    }

    public function createApplication(): App
    {
        $callableResolver = new CallableResolver($this->container);
        $routeCollector = new RouteCollector($this->responseFactory, $callableResolver, $this->container);

        $application = new App($this->responseFactory, $this->container, $callableResolver, $routeCollector);
        $this->configurator->configureApplication($application);
        $application->addRoutingMiddleware();

        return $application;
    }
}
