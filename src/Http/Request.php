<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Slim\Psr7\Request as BaseRequest;
use Slim\Routing\RouteContext;
use SlimAPI\Exception\LogicException;
use SlimAPI\Routing\Route;

class Request extends BaseRequest
{
    use Message;

    public function getRoute(): Route
    {
        $route = $this->getAttribute(RouteContext::ROUTE);
        if ($route === null) {
            throw new LogicException('No matched route. Missing call $app->addRoutingMiddleware()?');
        }

        return $route;
    }
}
