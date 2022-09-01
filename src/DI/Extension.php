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
use Slim\Psr7\Factory\ResponseFactory;
use SlimAPI\App;
use SlimAPI\AppFactory;
use SlimAPI\Configurator\ChainConfigurator;
use SlimAPI\Error\JsonErrorRenderer;
use SlimAPI\Handlers\ErrorHandler;
use SlimAPI\Http\RequestFactory;
use SlimAPI\Http\Response;

class Extension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'configurators' => Expect::array(),
            'errors' => Expect::structure([
                'debugMode' => Expect::bool(false),
                'displayErrorDetails' => Expect::bool(false),
                'handler' => Expect::string(ErrorHandler::class),
                'logErrorDetails' => Expect::bool(true),
                'logErrors' => Expect::bool(true),
                'middleware' => Expect::string(ErrorMiddleware::class),
                'renderer' => Expect::string(JsonErrorRenderer::class),
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
            ->setFactory(ResponseFactory::class)
            ->addSetup(new Statement(ResponseFactory::class . '::$responseClass = ?', [Response::class]));

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
        $config = $this->config->errors; // @phpstan-ignore-line
        if ($config->debugMode === true) {
            return;
        }

        $builder = $this->getContainerBuilder();

        $callableResolver = $builder->getDefinitionByType(CallableResolver::class);
        $responseFactory = $builder->getDefinitionByType(ResponseFactory::class);

        $errorRenderer = $builder->addDefinition($this->prefix('errorRenderer'))
            ->setFactory($config->renderer);

        $errorHandler = $builder->addDefinition($this->prefix('errorHandler'))
            ->setFactory($config->handler, [$callableResolver, $responseFactory])
            ->addSetup('registerErrorRenderer', ['application/json', $errorRenderer])
            ->addSetup('setDefaultErrorRenderer', ['application/json', $errorRenderer]);

        $errorMiddleware = $builder->addDefinition($this->prefix('errorMiddleware'))
            ->setFactory($config->middleware, [
                $callableResolver,
                $responseFactory,
                $config->displayErrorDetails,
                $config->logErrors,
                $config->logErrorDetails,
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
