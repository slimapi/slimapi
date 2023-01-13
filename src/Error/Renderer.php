<?php

declare(strict_types=1);

namespace SlimAPI\Error;

use Fig\Http\Message\StatusCodeInterface;
use Slim\Exception\HttpException;
use SlimAPI\Exception\Http\Exception;
use SlimAPI\Exception\Http\Generator;
use SlimAPI\Exception\Validation\RequestException;
use SlimAPI\Routing\Configurator;
use Throwable;

class Renderer implements RendererInterface
{
    use Generator;

    protected string $defaultErrorTitle = 'SlimAPI application error.';

    protected function generateError(Throwable $exception): array
    {
        if ($exception instanceof HttpException) {
            $code = $exception->getCode();
            $error = [
                'code' => $code,
                'error' => $this->getErrorFromCode($code),
                'message' => $exception->getMessage(),
            ];

        } elseif ($exception instanceof RequestException) {
            $error = [
                'code' => $exception->getCode(),
                'error' => $exception->getError(),
                'validation' => $exception->getValidator()->getErrors(),
                'message' => $exception->getValidator()->getErrorMessage(),
            ];

        } elseif ($exception instanceof Exception) {
            $error = [
                'code' => $exception->getCode(),
                'error' => $exception->getError(),
                'message' => $exception->getMessage(),
            ];

        } else {
            $error = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'error' => 'INTERNAL_SERVER_ERROR',
                'message' => $this->defaultErrorTitle,
            ];
        }

        $error['id'] = spl_object_hash($exception);
        return $error;
    }

    protected function traceParse(Throwable $exception): array
    {
        $data = [];
        foreach (explode(PHP_EOL, $exception->getTraceAsString()) as $trace) {
            if (str_contains($trace, Configurator::class)) {
                break;
            }

            $data[] = $trace;
        }

        return $data;
    }

    public function __invoke(Throwable $exception, bool $displayErrorDetails, bool $displayAsString = true): array|string
    {
        $error = $this->generateError($exception);

        if ($displayErrorDetails) {
            $error['exception'] = [
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'type' => $exception::class,
                'trace' => $this->traceParse($exception),
            ];
        }

        return $displayAsString
            ? (string) json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : $error;
    }
}
