<?php

declare(strict_types=1);

namespace SlimAPI\Application;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class ApplicationFactory
{
    private ResponseFactoryInterface $responseFactory;

    private ContainerInterface $container;

    private ApplicationConfigurator $configurator;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ContainerInterface $container,
        ApplicationConfigurator $configurator
    )
    {
        $this->responseFactory = $responseFactory;
        $this->container = $container;
        $this->configurator = $configurator;
    }

    public function createApplication(): Application
    {
        $app = new Application($this->responseFactory, $this->container);
        $this->configurator->configureApplication($app);
        return $app;
    }
}
