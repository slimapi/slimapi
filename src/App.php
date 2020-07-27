<?php

declare(strict_types=1);

namespace SlimAPI;

use Psr\Http\Message\ServerRequestInterface;
use Slim\App as BaseApp;
use Slim\ResponseEmitter;
use SlimAPI\DI\ContainerAdapter;
use SlimAPI\Http\Response;

/**
 * @method ContainerAdapter getContainer()
 * @method Response handle(ServerRequestInterface $request)
 */
class App extends BaseApp
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
}
