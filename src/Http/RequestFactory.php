<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Slim\Psr7\Factory\ServerRequestFactory;

class RequestFactory extends ServerRequestFactory
{
    public static $requestClass = Request::class; // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
}
