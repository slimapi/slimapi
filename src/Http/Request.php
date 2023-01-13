<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Psr\Http\Message\StreamInterface;
use Slim\Routing\RouteContext;
use SlimAPI\Exception\LogicException;
use SlimAPI\Routing\Route;

/**
 * @method Request withAttribute(string $name, mixed $value)
 * @method Request withBody(StreamInterface $body)
 * @method Request withHeader(string $name, mixed $value)
 */
class Request extends \Slim\Psr7\Request
{
    use Message;

    public const ATTRIBUTE_VALIDATION_SCHEMA = '__validation__';

    public function getRoute(): Route
    {
        $route = $this->getAttribute(RouteContext::ROUTE);
        if ($route === null) {
            throw new LogicException('No matched route. Missing call $app->addRoutingMiddleware()?');
        }

        return $route;
    }

    public function getValidationSchema(): array
    {
        return $this->getAttribute(self::ATTRIBUTE_VALIDATION_SCHEMA, []);
    }
}
