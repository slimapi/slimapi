<?php

declare(strict_types=1);

namespace SlimAPI;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\CallableResolver;
use SlimAPI\Configurator\ConfiguratorInterface;
use SlimAPI\Routing\RouteCollector;

class AppFactory
{
    private ResponseFactoryInterface $responseFactory;

    private ContainerInterface $container;

    private ConfiguratorInterface $configurator;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ContainerInterface $container,
        ConfiguratorInterface $configurator
    )
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
