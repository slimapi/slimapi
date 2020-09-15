<?php

declare(strict_types=1);

namespace SlimAPI\Validation\Exception;

use Fig\Http\Message\StatusCodeInterface;

class RequestException extends Exception
{
    protected int $defaultCode = StatusCodeInterface::STATUS_BAD_REQUEST;
}
