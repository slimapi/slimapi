<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class UnauthorizedException extends Exception
{
    private const DEFAULT_CHALLENGE = 'Bearer';

    public function __construct(string $message, string $challenge = self::DEFAULT_CHALLENGE, ?Throwable $previous = null)
    {
        $headers = ['WWW-Authenticate' => $challenge];

        parent::__construct($message, StatusCodeInterface::STATUS_UNAUTHORIZED, null, $headers, $previous);
    }
}
