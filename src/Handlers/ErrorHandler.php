<?php

declare(strict_types=1);

namespace SlimAPI\Handlers;

use Slim\Handlers\ErrorHandler as BaseErrorHandler;
use SlimAPI\Exception\Http\Exception;

class ErrorHandler extends BaseErrorHandler
{
    protected function determineStatusCode(): int
    {
        $code = parent::determineStatusCode();

        if ($code === 500 && $this->exception instanceof Exception) {
            $code = $this->exception->getCode();
        }

        return $code;
    }
}
