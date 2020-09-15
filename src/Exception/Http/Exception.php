<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Fig\Http\Message\StatusCodeInterface;
use ReflectionClass;
use SlimAPI\Exception\Exception as BaseException;
use SlimAPI\Exception\InvalidArgumentException;
use Throwable;

class Exception extends BaseException
{
    protected string $error;

    protected array $headers;

    public function __construct(string $msg, int $code = 500, ?string $error = null, array $headers = [], ?Throwable $prev = null)
    {
        if ($error === null) {
            $reflection = new ReflectionClass(StatusCodeInterface::class);
            $constants = array_flip($reflection->getConstants());

            if (!isset($constants[$code])) {
                throw new InvalidArgumentException('Argument $error has to be set.');
            }

            $error = str_replace('STATUS_', '', $constants[$code]);
        }

        $this->error = $error;
        $this->headers = $headers;

        parent::__construct($msg, $code, $prev);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
