<?php

declare(strict_types=1);

namespace SlimAPI\Error;

use Psr\Log\LogLevel;
use Slim\Handlers\ErrorHandler;
use SlimAPI\Exception\Http\Exception;

class Handler extends ErrorHandler
{
    protected string $defaultLogLevel = LogLevel::ERROR;
    protected string $defaultLogContextAttribute = LogLevel::ERROR;

    protected function determineStatusCode(): int
    {
        $code = parent::determineStatusCode();

        if ($code === 500 && $this->exception instanceof Exception) {
            $code = $this->exception->getCode();
        }

        return $code;
    }

    protected function determineLogLevel(): string
    {
        return $this->defaultLogLevel;
    }

    protected function determineLogMessage(): string
    {
        return $this->exception->getMessage();
    }

    protected function determineLogContextAttribute(): string
    {
        return $this->defaultLogContextAttribute;
    }

    protected function writeToErrorLog(): void
    {
        /** @var RendererInterface $renderer */
        $renderer = $this->callableResolver->resolve($this->logErrorRenderer);
        $error = $renderer($this->exception, $this->logErrorDetails, false);

        $this->logger->log(
            $this->determineLogLevel(),
            $this->determineLogMessage(),
            [$this->determineLogContextAttribute() => $error],
        );
    }
}
