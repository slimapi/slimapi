<?php

declare(strict_types=1);

namespace SlimAPI\Exception\Http;

use Throwable;

class Exception extends \SlimAPI\Exception\Exception
{
    use Generator;

    protected string $error;

    protected array $headers;

    public function __construct(string $msg, int $code = 500, ?string $error = null, array $headers = [], ?Throwable $prev = null)
    {
        $this->error = $error ?? $this->getErrorFromCode($code);
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
