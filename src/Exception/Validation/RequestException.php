<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Validation;

use Fig\Http\Message\StatusCodeInterface;

class RequestException extends Exception
{
    protected int $defaultCode = StatusCodeInterface::STATUS_BAD_REQUEST;
}
