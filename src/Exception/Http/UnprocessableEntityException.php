<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class UnprocessableEntityException extends Exception
{
    public function __construct(string $error, ?string $message = null, ?Throwable $previous = null)
    {
        if ($message === null) {
            $message = ucfirst(strtolower(str_replace('_', ' ', $error))) . '.';
        }

        parent::__construct($message, StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY, $error, [], $previous);
    }
}
