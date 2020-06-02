<?php

declare(strict_types=1);

namespace SlimAPI;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use SlimAPI\Configurator\ConfiguratorInterface;

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
        $app = new App($this->responseFactory, $this->container);
        $this->configurator->configureApplication($app);
        return $app;
    }
}
