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
use Slim\Psr7\Factory\ResponseFactory;
use SlimAPI\App;
use SlimAPI\AppFactory;
use SlimAPI\Configurator\ChainConfigurator;
use SlimAPI\Http\RequestFactory;
use SlimAPI\Http\Response;

class Extension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'configurators' => Expect::array(),
        ]);
    }

    public function loadConfiguration(): void
    {
        $this->setupApplication();
        $this->setupConfigurator();
        $this->setupRouting();
    }

    private function setupApplication(): void
    {
        $builder = $this->getContainerBuilder();

        $container = $builder->addDefinition($this->prefix('container'))
            ->setFactory(ContainerAdapter::class, [$this->name]);

        $builder->addDefinition($this->prefix('request'))
            ->setType(ServerRequestInterface::class)
            ->setFactory(RequestFactory::class . '::createFromGlobals');

        $responseFactory = $builder->addDefinition($this->prefix('responseFactory'))
            ->setFactory(ResponseFactory::class)
            ->addSetup(new Statement(ResponseFactory::class . '::$responseClass = ?', [Response::class]));

        $builder->addDefinition($this->prefix('applicationFactory'))
            ->setFactory(AppFactory::class, [$responseFactory, $container]);

        $builder->addDefinition($this->prefix('application'))
            ->setFactory($this->prefix('@applicationFactory::createApplication'));
    }

    private function setupConfigurator(): void
    {
        $builder = $this->getContainerBuilder();

        $chainConfigurator = $builder->addDefinition($this->prefix('chainConfigurator'))
            ->setFactory(ChainConfigurator::class);

        foreach ($this->config->configurators as $configurator) { // @phpstan-ignore-line
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

        $this->getApplicationDefinition()
            ->addSetup('addChainConfigurator');
    }

    private function setupRouting(): void
    {
        $this->getApplicationDefinition()
            ->addSetup('addRoutingMiddleware');
    }

    private function getApplicationDefinition(): ServiceDefinition
    {
        /** @var ServiceDefinition $definition */
        $definition = $this->getContainerBuilder()->getDefinitionByType(App::class);
        return $definition;
    }
}
