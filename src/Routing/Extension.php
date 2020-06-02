<?php

declare(strict_types=1);

namespace SlimAPI\Routing;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SlimAPI\Configurator\ChainConfigurator;

class Extension extends CompilerExtension
{
    public function loadConfiguration(): void
    {
        if ($this->config === []) {
            return;
        }

        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $configurator = $builder->addDefinition($this->prefix('configurator'))
            ->setFactory(Configurator::class, [$config]);

        /** @var ServiceDefinition $definition */
        $definition = $builder->getDefinitionByType(ChainConfigurator::class);
        $definition->addSetup('addConfigurator', [$configurator, true]);
    }
}
