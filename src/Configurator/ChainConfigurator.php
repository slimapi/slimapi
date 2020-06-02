<?php

declare(strict_types=1);

namespace SlimAPI\Configurator;

use SlimAPI\App;

/**
 * @link https://github.com/o2ps/SlimNetteBridge/blob/1a594a/src/Application/ChainApplicationConfigurator.php
 */
class ChainConfigurator implements ConfiguratorInterface
{
    /** @var ConfiguratorInterface[] */
    private array $configurators = [];

    public function addConfigurator(ConfiguratorInterface $configurator, bool $prepend = false): void
    {
        if ($prepend) {
            array_unshift($this->configurators, $configurator);
        } else {
            $this->configurators[] = $configurator;
        }
    }

    public function configureApplication(App $application): void
    {
        foreach ($this->configurators as $configurator) {
            $configurator->configureApplication($application);
        }
    }

    public function getConfigurators(): array
    {
        return $this->configurators;
    }
}
