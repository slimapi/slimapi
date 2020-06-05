<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Functional\_fixtures;

use SlimAPI\App;
use SlimAPI\Configurator\ConfiguratorInterface;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;

class TestConfigurator implements ConfiguratorInterface
{
    public function configureApplication(App $application): void
    {
        $application->get('/test-uri-configurator', static function (Request $req, Response $res): Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
            return $res->withNoContent();
        });
    }
}
