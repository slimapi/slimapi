<?php

declare(strict_types=1);

namespace SlimAPI\Application;

/**
 * @link https://github.com/o2ps/SlimNetteBridge/blob/1a594a/src/Application/ChainApplicationConfigurator.php
 */
class ChainConfigurator implements ApplicationConfigurator
{
    /** @var ApplicationConfigurator[] */
    private array $configurators = [];

    public function addConfigurator(ApplicationConfigurator $configurator, bool $prepend = false): void
    {
        if ($prepend) {
            array_unshift($this->configurators, $configurator);
        } else {
            $this->configurators[] = $configurator;
        }
    }

    public function configureApplication(Application $application): void
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
