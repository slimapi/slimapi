<?php

declare(strict_types=1);

namespace SlimAPI\Tests\Integration\_fixtures;

use SlimAPI\Application\Application;
use SlimAPI\Application\ApplicationConfigurator;
use SlimAPI\Http\Request;
use SlimAPI\Http\Response;

class TestConfigurator implements ApplicationConfigurator
{
    public function configureApplication(Application $application): void
    {
        $application->get('/test-uri-configurator', static function (Request $req, Response $res): Response { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
            return $res->withNoContent();
        });
    }
}
