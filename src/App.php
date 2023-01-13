<?php

declare(strict_types=1);

namespace SlimAPI;

use Psr\Http\Message\ServerRequestInterface;
use Slim\ResponseEmitter;
use SlimAPI\Configurator\ChainConfigurator;
use SlimAPI\DI\ContainerAdapter;
use SlimAPI\Http\Response;

/**
 * @method ContainerAdapter getContainer()
 * @method Response handle(ServerRequestInterface $request)
 */
class App extends \Slim\App
{
    public function run(?ServerRequestInterface $request = null): void
    {
        if ($request === null) {
            $request = $this->getContainer()->getByType(ServerRequestInterface::class);
        }

        $response = $this->handle($request);
        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }

    public function addChainConfigurator(): void
    {
        $chainConfigurator = $this->getContainer()->getByType(ChainConfigurator::class);
        $chainConfigurator->configureApplication($this);
    }
}
