<?php

declare(strict_types=1);

namespace SlimAPI\Http;

use Slim\Psr7\Factory\ServerRequestFactory;

class RequestFactory extends ServerRequestFactory
{
    /** @phpstan-ignore-next-line */
    public static $requestClass = Request::class; // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint
}
