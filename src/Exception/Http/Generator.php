<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Fig\Http\Message\StatusCodeInterface;
use ReflectionClass;
use SlimAPI\Exception\InvalidArgumentException;

trait Generator
{
    public function getErrorFromCode(int $code): string
    {
        $reflection = new ReflectionClass(StatusCodeInterface::class);
        $constants = array_flip($reflection->getConstants());

        if (!isset($constants[$code])) {
            throw new InvalidArgumentException('Cannot guess $error, set manually please.');
        }

        return str_replace('STATUS_', '', $constants[$code]);
    }
}
