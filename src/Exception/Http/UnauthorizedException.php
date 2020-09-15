<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class UnauthorizedException extends Exception
{
    public function __construct(string $message, ?string $authenticate = null, ?Throwable $previous = null)
    {
        $headers = $authenticate === null
            ? []
            : ['WWW-Authenticate' => $authenticate]; // @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/WWW-Authenticate

        parent::__construct($message, StatusCodeInterface::STATUS_UNAUTHORIZED, null, $headers, $previous);
    }
}
