<?php

declare(strict_types=1);

namespace SlimAPI\Application;

use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\ResponseEmitter;
use SlimAPI\DI\ContainerAdapter;

class Application extends App
{
    public function getContainer(): ContainerAdapter
    {
        /** @var ContainerAdapter $container */
        $container = parent::getContainer();
        return $container;
    }

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
