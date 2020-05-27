<?php

declare(strict_types=1);

namespace SlimAPI\Application;

/**
 * @link https://github.com/o2ps/SlimNetteBridge/blob/1a594a/src/Application/ApplicationConfigurator.php
 */
interface ApplicationConfigurator
{
    public function configureApplication(Application $application): void;
}
