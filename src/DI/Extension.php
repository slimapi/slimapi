<?php

declare(strict_types=1);

namespace SlimAPI\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Reference;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Psr\Http\Message\ServerRequestInterface;
use Slim\CallableResolver;
use Slim\Middleware\ErrorMiddleware;
use SlimAPI\App;
use SlimAPI\AppFactory;
use SlimAPI\Configurator\ChainConfigurator;
use SlimAPI\Error\Handler;
use SlimAPI\Error\Renderer;
use SlimAPI\Http\RequestFactory;
use SlimAPI\Http\ResponseFactory;

class Extension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'configurators' => Expect::array(),
            'errors' => Expect::structure([
                'displayDetails' => Expect::bool(false),
                'enableHandler' => Expect::bool(true),
                'handler' => Expect::string(Handler::class),
                'middleware' => Expect::string(ErrorMiddleware::class),
                'renderer' => Expect::string(Renderer::class),
            ]),
            'logs' => Expect::structure([
                'displayDetails' => Expect::bool(false),
                'enableLogger' => Expect::bool(true),
                'renderer' => Expect::string(Renderer::class),
            ]),
        ]);
    }

    public function loadConfiguration(): void
    {
        $this->setupApplication();
        $this->setupConfigurator();
        $this->setupRouting();
        $this->setupErrorHandling();
    }

    private function setupApplication(): void
    {
        $builder = $this->getContainerBuilder();

        $container = $builder->addDefinition($this->prefix('container'))
            ->setFactory(ContainerAdapter::class, [$this->name]);

        $callableResolver = $builder->addDefinition($this->prefix('callableResolver'))
            ->setFactory(CallableResolver::class, [$container]);

        $builder->addDefinition($this->prefix('request'))
            ->setType(ServerRequestInterface::class)
            ->setFactory(RequestFactory::class . '::createFromGlobals');

        $responseFactory = $builder->addDefinition($this->prefix('responseFactory'))
            ->setFactory(ResponseFactory::class);

        $builder->addDefinition($this->prefix('applicationFactory'))
            ->setFactory(AppFactory::class, [$responseFactory, $container, $callableResolver]);

        $builder->addDefinition($this->prefix('application'))
            ->setFactory($this->prefix('@applicationFactory::createApplication'));
    }

    private function setupConfigurator(): void
    {
        $builder = $this->getContainerBuilder();

        $chainConfigurator = $builder->addDefinition($this->prefix('chainConfigurator'))
            ->setFactory(ChainConfigurator::class);

        $config = $this->config->configurators; // @phpstan-ignore-line
        foreach ($config as $configurator) {
            if (!$configurator instanceof Statement) {
                $configurator = new Statement($configurator);
            }

            if ($configurator->getEntity() instanceof Reference) {
                $entityName = $configurator->getEntity()->getValue();
            } else {
                /** @var string $entityName */
                $entityName = $configurator->getEntity();
            }

            /** @var string $name */
            $name = preg_replace('#[^a-zA-Z0-9_]+#', '_', $entityName);

            $configuratorService = $builder
                ->addDefinition($this->prefix($name))
                ->setFactory($configurator);

            $chainConfigurator->addSetup('addConfigurator', [$configuratorService]);
        }

        $this->getApplicationDefinition()->addSetup('addChainConfigurator');
    }

    private function setupRouting(): void
    {
        $this->getApplicationDefinition()->addSetup('addRoutingMiddleware');
    }

    private function setupErrorHandling(): void
    {
        $handlerConfig = $this->config->errors; // @phpstan-ignore-line
        $loggerConfig = $this->config->logs; // @phpstan-ignore-line
        if ($handlerConfig->enableHandler === false) {
            return;
        }

        $builder = $this->getContainerBuilder();

        $callableResolver = $builder->getDefinitionByType(CallableResolver::class);
        $responseFactory = $builder->getDefinitionByType(ResponseFactory::class);

        $errorRenderer = $builder->addDefinition($this->prefix('errorRenderer'))
            ->setFactory($handlerConfig->renderer);

        $logErrorRenderer = $builder->addDefinition($this->prefix('logErrorRenderer'))
            ->setFactory($loggerConfig->renderer);

        $errorHandler = $builder->addDefinition($this->prefix('errorHandler'))
            ->setFactory($handlerConfig->handler, [$callableResolver, $responseFactory])
            ->addSetup('registerErrorRenderer', ['application/json', $errorRenderer])
            ->addSetup('setDefaultErrorRenderer', ['application/json', $errorRenderer])
            ->addSetup('setLogErrorRenderer', [$logErrorRenderer]);

        $errorMiddleware = $builder->addDefinition($this->prefix('errorMiddleware'))
            ->setFactory($handlerConfig->middleware, [
                $callableResolver,
                $responseFactory,
                $handlerConfig->displayDetails,
                $loggerConfig->enableLogger,
                $loggerConfig->displayDetails,
            ])
            ->addSetup('setDefaultErrorHandler', [$errorHandler]);

        $this->getApplicationDefinition()->addSetup('add', [$errorMiddleware]);
    }

    private function getApplicationDefinition(): ServiceDefinition
    {
        /** @var ServiceDefinition $definition */
        $definition = $this->getContainerBuilder()->getDefinitionByType(App::class);
        return $definition;
    }
}
