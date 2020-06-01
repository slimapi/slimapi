<?php

declare(strict_types=1);

namespace SlimAPI\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Reference;
use Nette\DI\Definitions\Statement;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ResponseFactory;
use SlimAPI\Application\ApplicationFactory;
use SlimAPI\Application\ChainConfigurator;
use SlimAPI\Http\RequestFactory;
use SlimAPI\Http\Response;

class Extension extends CompilerExtension
{
    /** @var array */
    protected array $defaults = [
        'configurators' => [],
    ];

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $container = $builder->addDefinition($this->prefix('container'))
            ->setFactory(ContainerAdapter::class, [$this->name]);

        $chainConfigurator = $builder->addDefinition($this->prefix('chainConfigurator'))
            ->setFactory(ChainConfigurator::class);

        foreach ($config['configurators'] as $configurator) {
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

        $builder->addDefinition($this->prefix('request'))
            ->setType(ServerRequestInterface::class)
            ->setFactory(RequestFactory::class . '::createFromGlobals');

        $responseFactory = $builder->addDefinition($this->prefix('responseFactory'))
            ->setFactory(ResponseFactory::class)
            ->addSetup(new Statement(ResponseFactory::class . '::$responseClass = ?', [Response::class]));

        $builder->addDefinition($this->prefix('applicationFactory'))
            ->setFactory(ApplicationFactory::class, [$responseFactory, $container, $chainConfigurator]);

        $builder->addDefinition($this->prefix('application'))
            ->setFactory($this->prefix('@applicationFactory::createApplication'));
    }
}
