<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class BadRequestException extends Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, StatusCodeInterface::STATUS_BAD_REQUEST, null, [], $previous);
    }
}
